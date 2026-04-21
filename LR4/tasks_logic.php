<?php
// tasks_logic.php

// ============================================
// ЗАДАЧА 1: Прямая речь (абзацы с длинного тире)
// ============================================
function extractDirectSpeech($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<meta charset="utf-8">' . $html);
    libxml_clear_errors();
    
    $paragraphs = $dom->getElementsByTagName('p');
    $speech = [];
    
    foreach ($paragraphs as $p) {
        $text = trim($p->textContent);
        if (mb_substr($text, 0, 1) == '—') {
            $speech[] = $dom->saveHTML($p);
        }
    }
    
    if (count($speech) > 0) {
        return '<div class="task1-result"><h3>Прямая речь:</h3>' . implode('', $speech) . '</div>';
    } else {
        return '<div class="task1-result"><p>Прямая речь не найдена</p></div>';
    }
}

// ============================================
// ЗАДАЧА 2: Запятые перед "а" и "но" + многоточия
// ============================================
function processPunctuation($html) {
    // Сначала обрабатываем запятые
    $patterns = [
        '/([а-яА-Яa-zA-Z0-9.,!?;:])\s+(а)\s+/u' => '$1, а ',
        '/([а-яА-Яa-zA-Z0-9.,!?;:])\s+(но)\s+/u' => '$1, но ',
        '/([а-яА-Яa-zA-Z0-9.,!?;:])\s+(а)([.!?;:]|$)/u' => '$1, а$3',
        '/([а-яА-Яa-zA-Z0-9.,!?;:])\s+(но)([.!?;:]|$)/u' => '$1, но$3',
    ];
    
    $text = $html;
    foreach ($patterns as $pattern => $replacement) {
        $text = preg_replace($pattern, $replacement, $text);
    }
    
    // Потом заменяем три точки на многоточие
    $text = preg_replace('/\.{3,}/u', '…', $text);
    
    return '<div class="task2-result"><h3>Текст с исправлениями:</h3>' . $text . '</div>';
}

// ============================================
// ЗАДАЧА 3: Оглавление по заголовкам
// ============================================
function generateTOC($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<meta charset="utf-8">' . $html);
    libxml_clear_errors();
    
    $xpath = new DOMXPath($dom);
    $headings = $xpath->query('//h1 | //h2 | //h3');
    
    $toc = '<div class="task3-result"><h3>Оглавление:</h3><ul class="toc">';
    $prev_level = 0;
    $heading_index = 0;
    
    foreach ($headings as $h) {
        $level = (int)substr($h->nodeName, 1);
        $text = trim($h->textContent);
        
        // Обрезаем длинные заголовки
        $display_text = mb_strlen($text) > 50 ? mb_substr($text, 0, 50) . '…' : $text;
        
        // Создаем якорь
        $anchor = 'heading-' . $heading_index;
        $h->setAttribute('id', $anchor);
        
        // Формируем оглавление
        while ($level < $prev_level) {
            $toc .= '</ul></li>';
            $prev_level--;
        }
        
        if ($level > $prev_level) {
            if ($prev_level > 0) {
                $toc .= '<ul>';
            }
        } elseif ($level == $prev_level && $prev_level > 0) {
            $toc .= '</li>';
        }
        
        $toc .= '<li><a href="#' . $anchor . '" class="toc-level-' . $level . '">' . htmlspecialchars($display_text) . '</a>';
        
        $prev_level = $level;
        $heading_index++;
    }
    
    // Закрываем все открытые уровни
    while ($prev_level > 0) {
        if ($prev_level > 1) {
            $toc .= '</ul></li>';
        } else {
            $toc .= '</li>';
        }
        $prev_level--;
    }
    
    $toc .= '</ul></div>';
    
    // Сохраняем HTML с якорями
    $modified_html = '';
    foreach ($dom->childNodes as $child) {
        $modified_html .= $dom->saveHTML($child);
    }
    
    return [$toc, $modified_html];
}

// ============================================
// ЗАДАЧА 4: Фильтр запретных слов
// ============================================
function filterForbiddenWords($html) {
    $forbidden_words = ['пух', 'рот', 'делать', 'ехать', 'около', 'для'];
    
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<meta charset="utf-8">' . $html);
    libxml_clear_errors();
    
    $xpath = new DOMXPath($dom);
    $text_nodes = $xpath->query('//text()[not(ancestor::script)][not(ancestor::style)]');
    
    foreach ($text_nodes as $node) {
        $text = $node->nodeValue;
        $new_text = $text;
        
        foreach ($forbidden_words as $word) {
            // Ищем слово как корень (не внутри другого слова)
            $pattern = '/(?<!\pL)(' . preg_quote($word, '/') . ')(?=\pL*(\b|$))/ui';
            $new_text = preg_replace_callback($pattern, function($matches) {
                return str_repeat('#', mb_strlen($matches[1]));
            }, $new_text);
        }
        
        if ($new_text !== $text) {
            $new_node = $dom->createTextNode($new_text);
            $node->parentNode->replaceChild($new_node, $node);
        }
    }
    
    $filtered_html = '';
    foreach ($dom->childNodes as $child) {
        $filtered_html .= $dom->saveHTML($child);
    }
    
    $word_list = implode(', ', $forbidden_words);
    return '<div class="task4-result"><h3>Текст с фильтром (запрещены: ' . $word_list . '):</h3>' . $filtered_html . '</div>';
}

// ============================================
// ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
// ============================================

// Очистка HTML от лишних тегов
function cleanHtmlContent($html) {
    $html = preg_replace('/^.*?<body[^>]*>/is', '', $html);
    $html = preg_replace('/<\/body>.*$/is', '', $html);
    $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
    $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
    return trim($html);
}

// Получение предустановленных текстов
function getPresetText($presetNum) {
    $presets = [
        1 => [
            'title' => 'Киноринхи',
            'content' => '<h1>Киноринхи</h1>
<h2>Общая характеристика</h2>
<p>Кинори́нхи (лат. Kinorhyncha) — класс беспозвоночных животных. Ротовой аппарат у них устроен особым образом. Около дна они передвигаются медленно.</p>
<p>— Это интересные существа, — сказал ученый.</p>
<p>— Да, действительно, — ответил его коллега. Ротовой конус выдвигается при питании.</p>
<h2>Анатомия</h2>
<p>Тело разделено на сегменты. Интересно, что слово "коловорот" не связано с ротовым аппаратом, хотя содержит корень "рот". Пух и перья у них отсутствуют.</p>'
        ],
        2 => [
            'title' => 'Статья о театре',
            'content' => '<h1>Культурный слой</h1>
<h2>Интервью с режиссером</h2>
<p>— Как вы оцениваете современный театр? — спросил журналист. Что делать, если зритель не понимает?</p>
<p>— Театр жив, пока есть зрители, — ответил режиссер. Для нас важно говорить на понятном языке. Около театра всегда много людей.</p>
<p>— А эксперименты на сцене? — продолжил журналист. Ехать на гастроли или оставаться в родном городе?</p>
<h2>Зрители</h2>
<p>Зрители ждут новых открытий. Пух и прах - это не про театр!</p>'
        ],
        3 => [
            'title' => 'Винни-Пух',
            'content' => '<h1>Винни-Пух и все-все-все</h1>
<h2>Глава первая</h2>
<p>— Папа, как насчет сказки? — спросил Кристофер Робин. Пух очень хочет послушать сказку про себя.</p>
<p>— Что мне делать? — спросил папа. Рассказывать сказку?</p>
<p>— Ты не мог бы рассказать Винни-Пуху сказочку? Ему очень хочется! Для него это важно. Около его домика соберемся слушать.</p>
<h2>Разговор о сказке</h2>
<p>— Может быть, и мог бы, — сказал папа. — Ехать нам никуда не надо.</p>
<p>— Про него самого, конечно! — сказал Кристофер Робин. Пух так долго ждал...</p>'
        ]
    ];
    
    if (isset($presets[$presetNum])) {
        return $presets[$presetNum];
    }
    
    return ['title' => 'Неизвестно', 'content' => '<p>Нет такого preset</p>'];
}
?>