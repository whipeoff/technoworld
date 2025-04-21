<?php
namespace Core\Controllers;

use Core\Models\Request as ClientRequest;
use function sanitizeInput;
use function validateInputLength;

class RequestController extends BaseController
{
    public function showForm(): void
    {
        $this->render('request/form.php', [
            'csrf' => $this->csrf->getInputHTML(),
        ], 'base');
    }

    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            abort(405, 'Метод не поддерживается');
        }

        $this->rateLimiter->check('request_submit', 5);

        $name    = sanitizeInput($_POST['name']    ?? '');
        $phone   = sanitizeInput($_POST['phone']   ?? '');
        $comment = sanitizeInput($_POST['comment'] ?? '');

        $error = validateInputLength($name,    100, 'Имя')
              ?? validateInputLength($phone,   20,  'Телефон')
              ?? validateInputLength($comment,1000,'Комментарий');

        $digits = preg_replace('/\D+/', '', $phone);
        if (!$error && (strlen($digits) !== 11 || $digits[0] !== '7')) {
            $error = 'Телефон должен содержать 11 цифр и начинаться с +7';
        }

        if ($error) {
            $this->render('request/form.php', [
                'csrf'    => $this->csrf->getInputHTML(),
                'error'   => $error,
                'old'     => compact('name','phone','comment'),
            ], 'base');
            return;
        }

        try {
            ClientRequest::create($this->pdo, [
                'name'    => $name,
                'phone'   => $phone,
                'comment' => $comment,
            ]);
        } catch (\Throwable $e) {
            abort(500, 'Не удалось сохранить заявку. Попробуйте позже.');
        }

        header('Location: /request/success');
        exit;
    }

    public function success(): void
    {
        $this->render('request/success.php', [
            'title'           => 'Заявка принята',
            'metaDescription' => 'Спасибо! Мы получили вашу заявку.',
            'metaRobots'      => 'noindex, nofollow',
            'canonicalUrl'    => 'http://techno-world.free.nf/request/success',
        ], 'base');
    }
}
