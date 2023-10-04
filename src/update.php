<?php

require_once __DIR__ . '/lib/mysqli.php';

function updateReview($link, $review)
{
    $sql = <<<EOT
    UPDATE reviews SET 
    title = "{$review['title']}", 
    author = "{$review['author']}", 
    status = "{$review['status']}", 
    score = "{$review['score']}", 
    summary = "{$review['summary']}" 
    WHERE id="{$review['id']}"
    EOT;

    $result = mysqli_query($link, $sql);
    if (!$result) {
        error_log('Error: fail to update review');
        error_log('Debuuging Error: ' . mysqli_error($link));
    }
}

function validate($review)
{
    $errors = [];

    if (!strlen($review['title'])) {
        $errors['title'] = '書籍名を入力してください';
    } elseif (strlen($review['title']) > 255) {
        $errors['title'] = '書籍名は255文字以内で入力してください';
    }

    if (!strlen($review['author'])) {
        $errors['author'] = '著者名を入力してください';
    } elseif (strlen($review['author']) > 100) {
        $errors['author'] = '著者名は100文字以内で入力してください';
    }

    if (!strlen($review['status'])) {
        $errors['status'] = '読書状況を選択してください';
    }

    if ($review['score'] < 1 || 5 < $review['score']) {
        $errors['score'] = '評価は1以上5以下の整数で入力してください';
    }

    if (!strlen($review['summary'])) {
        $errors['summary'] = '感想を入力してください';
    } elseif (strlen($review['summary']) > 1000) {
        $errors['summary'] = '感想は1000文字以内で入力してください';
    }

    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review = [
        'id' => $_POST['id'],
        'title' => $_POST['title'],
        'author' => $_POST['author'],
        'status' => $_POST['status'],
        'score' => $_POST['score'],
        'summary' => $_POST['summary']
    ];

    $errors = validate($review);
    if (!count($errors)) {
        $link = dbConnect();
        updateReview($link, $review);
        mysqli_close($link);
        header("Location: index.php");
    }
}

$title = '読書ログの編集';
$content = __DIR__ . '/views/detail.php';
include __DIR__ . '/views/layout.php';
