<?php

function analyse_proto(string $file) {

    $fp = fopen($file, 'r');
    $protoline = preg_quote('/* {{{ proto public ');
    $prev_matched = false;
    $prev_matches = [];
    $methods = [];

    while ($line = fgets($fp)) {
        $matched = preg_match("#{$protoline}(?P<return>\w+)(?P<returnArray>\[\])* (?P<class>\w+)\:\:(?P<method>\w+)\(\[*(?P<parameterType>\w*)\]*\s*.*\)#", $line, $m);

        if ($matched) {
            $methods[$m['class']][$m['method']]['return'] = $m['return'];
            $methods[$m['class']][$m['method']]['returnArray'] = isset($m['returnArray']);

            if (!empty($m['parameterType'])) {
                $methods[$m['class']][$m['method']]['parameterType'] = $m['parameterType'];
            }
        }

        if ($prev_matched) {
            $comment_matched= preg_match('#\s*(.*)\*\/$#', $line, $c);
            if ($comment_matched) {
                if (isset($methods[$prev_matches['class']][$prev_matches['method']]['return'])) {
                    $methods[$prev_matches['class']][$prev_matches['method']]['comment'] = $c[1];
                }
            }
        }

        $prev_matches = $m;
        $prev_matched = $matched;
    }
    return $methods;
}
