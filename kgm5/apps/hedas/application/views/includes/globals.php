<?php
if (isDbAllowedWriteModule("dosya")) {
    $this->load->view("dosya_v/globals/add/index.php");
}
if (isDbAllowedWriteModule("gelengiden")) {
    $this->load->view("gelengiden_v/globals/add/index.php");
}
if (isDbAllowedWriteModule("cezaiptal")) {
    $this->load->view("cezaiptal_v/globals/add/index.php");
}
