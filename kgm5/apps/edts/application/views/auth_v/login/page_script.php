<!--begin::Javascript-->
<script>var hostUrl = "<?php echo base_url('assets/'); ?>";</script>
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="<?php echo base_url('assets/plugins/global/plugins.bundle.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/scripts.bundle.js'); ?>"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Page Custom Javascript: form gerçek POST ile auth/do_login'e gider; general.js email alanı beklediği ve formu göndermediği için devre dışı -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  var form = document.getElementById("kt_sign_in_form");
  var btn = document.getElementById("kt_sign_in_submit");
  if (form && btn) {
    form.addEventListener("submit", function() {
      var u = (form.querySelector('[name="username"]') || {}).value || "";
      var p = (form.querySelector('[name="password"]') || {}).value || "";
      if (u.trim() && p.trim()) {
        btn.disabled = true;
        btn.querySelector(".indicator-label") && (btn.querySelector(".indicator-label").textContent = "Gönderiliyor...");
      }
    });
  }
});
</script>
<!--end::Page Custom Javascript-->
<!--begin::Page Popup Alert-->
<?php $this->load->view("includes/alert"); ?>
<!--end::Page Popup Alert-->
<!--end::Javascript-->
