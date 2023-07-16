<?php

get_header();

$loginTemplate = new LoginTemplate();
if (!QSS_Client::isTokenValid()){ $loginTemplate->handleFormSubmission(); }
echo $loginTemplate->render();

get_footer();

?>