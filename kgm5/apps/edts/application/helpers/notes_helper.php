<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Not etiketleri: kod => [label, badge_class]
 */
function getNoteTagOptions()
{
    return array(
        'unutma'      => array('label' => 'Unutma',      'class' => 'badge badge-light-warning'),
        'acil'        => array('label' => 'Acil',        'class' => 'badge badge-light-danger'),
        'onemli'      => array('label' => 'Önemli',      'class' => 'badge badge-light-primary'),
        'dikkat_et'   => array('label' => 'Dikkat Et',   'class' => 'badge badge-light-info'),
        'duzeltilecek' => array('label' => 'Düzeltilecek', 'class' => 'badge badge-light-secondary'),
        'sor'         => array('label' => 'Sor',         'class' => 'badge badge-light-success')
    );
}

function getNoteTagBadge($tag)
{
    $options = getNoteTagOptions();
    if (empty($tag) || !isset($options[$tag])) {
        return '';
    }
    $opt = $options[$tag];
    return '<span class="' . $opt['class'] . '">' . htmlspecialchars($opt['label']) . '</span>';
}
