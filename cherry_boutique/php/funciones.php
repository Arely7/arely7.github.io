<?php
function e($txt)
{
    return htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8');
}
function dinero($n)
{
    return '$' . number_format((float) $n, 2) . ' MXN';
}

function flash($tipo, $msg)
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'msg' => $msg];
}
function mostrarFlash()
{
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alerta ' . e($f['tipo']) . '">' . e($f['msg']) . '</div>';
    }
}
function claseColor($color)
{
    $mapa = ['rosa' => 'col rosa-bg', 'rojo' => 'col guinda-bg', 'guinda' => 'col guinda-bg', 'negro' => 'col negro', 'blanco' => 'col blanco-bg', 'cafe' => 'col cafe-bg', 'beige' => 'col rosa3-bg'];
    return $mapa[strtolower(trim($color))] ?? 'col rosa2-bg';
}
function claseColorInicio($color)
{
    $mapa = ['rosa' => 'puntorosaosc', 'rojo' => 'puntocafe', 'guinda' => 'puntocafe', 'negro' => 'puntonegro', 'blanco' => 'puntoblanco', 'cafe' => 'puntocafe', 'beige' => 'puntorosacla'];
    return $mapa[strtolower(trim($color))] ?? 'puntorosacla';
}