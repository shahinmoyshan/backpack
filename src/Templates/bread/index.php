<?php

// set layout, title, and breadcrumb
$template->layout('master')
    ->set('title', $bread->getPageTitle())
    ->set('breadcrumb', $bread->getBreadcrumbItems());

?>

<!-- Render the bread index START -->
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <?= $bread->render() ?>
</div>
<!-- Render the bread index END -->