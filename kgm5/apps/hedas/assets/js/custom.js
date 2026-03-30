$(document).ready(function(){

  $(".sortable").sortable();

  $(".isActive").change(function(){
      var $data = $(this).prop("checked");
      var $data_url = $(this).data("url");

      if(typeof $data !== "undefined" && typeof $data_url !== "undefined"){
          $.post($data_url, {data: $data}, function(response){});
      }

  })

  $(".sortable").on("sortupdate", function(event, ui){
      var $data     = $(this).sortable("serialize");
      var $data_url = $(this).data("url");

      $.post($data_url, {data: $data}, function(response){});
  })


  $(".remove-btn").click(function(e){
      $data_url = $(this).data("url");
      Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu işlemi geri alamayacaksınız!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, Sil!',
        cancelButtonText:   'Hayır'
      }).then((result) => {
        if (result.value) {
          window.location.href = $data_url;
        }
      });

  })

  $('.buyukYaz').keyup(function(){
    this.value=this.value.toUpperCase();
  });


})

function toastrAlert (type = "success", title = "", text = ""){
  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toastr-bottom-left",
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


  switch(type){
    case "success":
      toastr.success(text, title);
      break;
    case "error":
      toastr.error(text, title);
      break;
    default:
      toastr.success(text, title);
      break;
  }
}