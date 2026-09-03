<?php

declare(strict_types=1);

abstract class Controller
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Render view without layout.
     */
    protected function view(string $view, array $data = []): void
    {
        $path = BASE_PATH . '/views/' . $view . '.php';

        if (!file_exists($path)) {
            abort(404);
        }

        extract($data, EXTR_SKIP);
        require $path;
    }

    /**
     * Render view inside a layout.
     * $contentView is injected as $content variable into the layout.
     */
    protected function viewWithLayout(string $view, string $layout = 'layouts/main', array $data = []): void
    {
        $contentView = BASE_PATH . '/views/' . $view . '.php';
        $layoutFile = BASE_PATH . '/views/' . $layout . '.php';

        if (!file_exists($contentView)) {
            abort(404);
        }
        if (!file_exists($layoutFile)) {
            // Fallback: render without layout
            extract($data, EXTR_SKIP);
            require $contentView;
            return;
        }

        // Extract for content view
        extract($data, EXTR_SKIP);

        ob_start();
        require $contentView;
        $content = (string) ob_get_clean();

        // Variables expected by layouts
        $pageTitle = $data['title'] ?? 'Pageon';
        $page = $data['page'] ?? '';

        // Make $title available as well for backward compat
        $title = $pageTitle;

        require $layoutFile;
    }

    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Validate request input.
     *
     * Supported rules:
     *   required | email | numeric | integer | min:N | max:N | unique:table,column,excludeId
     *
     * @return array<string,string>  field => error message
     */
    protected function validate(array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $raw = $_POST[$field] ?? '';
            $value = is_string($raw) ? trim($raw) : $raw;
            $label = str_replace('_', ' ', ucfirst($field));

            // Empty string and null are considered missing for `required`
            $isEmpty = $value === '' || $value === null;

            foreach (explode('|', $ruleSet) as $rule) {
                $rule = trim($rule);
                if ($rule === '') {
                    continue;
                }

                if ($rule === 'required') {
                    if ($isEmpty) {
                        $errors[$field] = "{$label} wajib diisi.";
                        break; // no need to check further rules
                    }
                    continue;
                }

                // Skip other checks if empty and not required
                if ($isEmpty) {
                    continue;
                }

                if ($rule === 'email') {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = "{$label} format email tidak valid.";
                    }
                } elseif ($rule === 'numeric') {
                    if (!is_numeric($value)) {
                        $errors[$field] = "{$label} harus berupa angka.";
                    }
                } elseif ($rule === 'integer') {
                    if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                        $errors[$field] = "{$label} harus berupa bilangan bulat.";
                    }
                } elseif (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (is_numeric($value)) {
                        if ((float) $value < $min) {
                            $errors[$field] = "{$label} minimal {$min}.";
                        }
                    } elseif (mb_strlen((string) $value) < $min) {
                        $errors[$field] = "{$label} minimal {$min} karakter.";
                    }
                } elseif (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if (is_numeric($value) && is_string($value) && ctype_digit($value)) {
                        // For numeric strings, check numeric value vs length? Prefer length for text
                        // We'll check string length for non-numeric interpretation
                        if (mb_strlen((string) $value) > $max) {
                            $errors[$field] = "{$label} maksimal {$max} karakter.";
                        }
                    } elseif (mb_strlen((string) $value) > $max) {
                        $errors[$field] = "{$label} maksimal {$max} karakter.";
                    }
                } elseif (str_starts_with($rule, 'unique:')) {
                    $params = explode(',', substr($rule, 7));
                    $table = trim($params[0] ?? '');
                    $column = trim($params[1] ?? $field);
                    $excludeId = isset($params[2]) ? trim($params[2]) : null;

                    // Basic identifier validation to prevent injection via rules (developer-controlled, but be safe)
                    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
                        $errors[$field] = "{$label} rule unique tidak valid.";
                        continue;
                    }

                    $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
                    $bind = [$value];

                    if ($excludeId !== null && $excludeId !== '') {
                        $sql .= ' AND id != ?';
                        $bind[] = $excludeId;
                    }

                    $stmt = $this->db->prepare($sql);
                    $stmt->execute($bind);

                    if ((int) $stmt->fetchColumn() > 0) {
                        $errors[$field] = "{$label} sudah digunakan.";
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Verify CSRF token for state-changing requests.
     */
    protected function verifyCsrf(): void
    {
        if (!verify_csrf()) {
            Session::flash('error', 'Sesi kadaluarsa. Silakan coba lagi.');
            // Redirect back if possible
            $referer = $_SERVER['HTTP_REFERER'] ?? null;
            if ($referer !== null && str_starts_with($referer, url(''))) {
                header('Location: ' . $referer);
            } else {
                redirect('/');
            }
            exit;
        }
    }

    protected function requireAuth(): void
    {
        if (!isAuth()) {
            Session::flash('error', 'Silakan login terlebih dahulu.');
            redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (!isAdmin()) {
            Session::flash('error', 'Anda tidak memiliki akses.');
            redirect('/');
        }
    }

    /**
     * Validate book/category existence helper.
     */
    protected function ensureExists(?array $record, string $message = 'Data tidak ditemukan.', string $redirectTo = '/'): never
    {
        if ($record === null) {
            Session::flash('error', $message);
            redirect($redirectTo);
        }
    }
}
