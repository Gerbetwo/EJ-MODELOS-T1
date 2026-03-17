<?php
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . '/modelos-udenar/');
    exit;
}
header('Location: /modelos-udenar/');
exit;