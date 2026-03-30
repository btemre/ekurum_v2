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


  $(".popup-btn").click(function(e){
      $data_url           = $(this).data("url");
      $data_confirm_text  = $(this).data("confirm-text");
      Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu işlemi geri alamayacaksınız!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: $data_confirm_text,
        cancelButtonText:   'Hayır'
      }).then((result) => {
        if (result.value) {
          window.location.href = $data_url;
        }
      });

  })




})
