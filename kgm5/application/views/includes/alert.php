<?php
$alert = $this->session->userdata("alert");
if ($alert) {
    if ($alert["type"] === "success") {
?>
        <script>
            // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
            Swal.fire({
                title: '<?php echo trim(replacePost($alert["title"])); ?>',
                text: '<?php echo trim(replaceHtml($alert["text"])); ?>',
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "Anladım.",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        </script>
    <?php
    } else {
    ?>
        <script>
            // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
            Swal.fire({
                title: '<?php echo trim(replacePost($alert["title"])); ?>',
                text: '<?php echo trim(replaceHtml($alert["text"])); ?>',
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Anladım.",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        </script>
    <?php
    }
}


$alertT = $this->session->userdata("alertToastr");
if ($alertT) {
    if ($alertT["type"] === "success") {
    ?>
        <script type="text/javascript">
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toastr-top-center",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            toastr.success('<?php echo trim(replacePost($alertT["text"])); ?>', '<?php echo trim(replacePost($alertT["title"])); ?>');
        </script>
    <?php
    } else {
    ?>
        <script type="text/javascript">
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toastr-top-center",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            toastr.error('<?php echo trim(replacePost($alertT["text"])); ?>', '<?php echo trim(replacePost($alertT["title"])); ?>');
        </script>
    <?php
    }
}

$alertlogin = $this->session->userdata("alertlogin");
if ($alertlogin) {
    if ($alertlogin["type"] === "success") {
    ?>
        <script>
            // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/

            Swal.fire({
                title: '<?php echo trim(replacePost($alertlogin["title"])); ?>',
                html: `<strong>Hoşgeldin <?php echo trim(replaceHtml($alertlogin["text"])); ?></strong> <br> 
                Herhangi bir sistem hatası ve ya bir mesaj bildirmek isterseniz  <span class="badge badge-warning"><a href="https://ekurum.hipporello.net/">Destek Merkezi</a></span> Üzerinden İletebilirsiniz. Bildiriminiz Yöneticilere ve Yazılımcılara İletilecektir.`,
                icon: "success",
                buttonsStyling: false,
                showCancelButton: false,
                confirmButtonText: "Tamam",
                customClass: {
                    confirmButton: "btn btn-success"
                }
            });
        </script>
    <?php
    } else {
    ?>
        <script>
            // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
            Swal.fire({
                title: '<?php echo trim(replacePost($alertlogin["title"])); ?>',
                text: '<?php echo trim(replaceHtml($alertlogin["text"])); ?>',
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Anladım.",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        </script>
<?php
    }
}
?>