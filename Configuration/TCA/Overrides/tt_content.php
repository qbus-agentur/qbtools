<?php

if (isset($GLOBALS['TCA']['tt_content']['columns']['tx_gridelements_children'])) {
    $GLOBALS['TCA']['tt_content']['columns']['tx_gridelements_children']['config']['appearance']['enabledControls']['new'] = true;
    $GLOBALS['TCA']['tt_content']['columns']['tx_gridelements_children']['config']['appearance']['levelLinksPosition'] = 'top';
}
