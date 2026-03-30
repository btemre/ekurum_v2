"use strict";
var KTAuthNewPassword=function(){
	var t,e,r,o,a=function(){
		return 100===o.getScore()
    };
    return{
        init:function(){
            t=document.querySelector("#kt_new_password_form"),
            e=document.querySelector("#kt_new_password_submit"),
            o=KTPasswordMeter.getInstance(t.querySelector('[data-kt-password-meter="true"]')),
            r=FormValidation.formValidation(
            t,
            {
                fields:{
                    password:{
                        validators:{
                            notEmpty:{
                                message:"Şifre Gerekli"
                                },
                                stringLength: {
                                    min		: 12,
                                    max		: 30,
                                    message: 'Değer geçerli bir parola değil!'
                                },
                                callback:{
                                    message:"Geçerli Bir Şifre Girin",
                                    callback:function(t){
                                                if(t.value.length>0)return a()
                                            }
                                        }
                                    }
                                },
                                "repassword":{
                                    validators:{
                                        notEmpty:{
                                            message:"Şifre Onayı Gerekli"
                                            },
                                            identical:{
                                                compare:function(){
                                                    return t.querySelector('[name="password"]').value
                                                },
                                                message:"Parola Tekrarı Aynı Değil"
                                            }
                                    }
                                },
                                toc:{}
                    },
                    plugins:{
                        trigger:new FormValidation.plugins.Trigger({
                            event:{password:!1}
                        }),
                        bootstrap:new FormValidation.plugins.Bootstrap5({
                            rowSelector:".fv-row",
                            eleInvalidClass:"",
                            eleValidClass:""
                        })
                    }
            }),
            e.addEventListener("click",(function(a){
                a.preventDefault(),
                r.revalidateField("password"),
                r.validate().then((function(r){
                    "Valid"==r?(e.setAttribute("data-kt-indicator","on"),
                    e.disabled=!0,setTimeout((function(){
                        e.removeAttribute("data-kt-indicator"),
                        e.disabled=!1,
                        Swal.fire({
                            text:"Parola Başarı İle Sıfırlandı!",
                            icon:"success",buttonsStyling:!1,
                            confirmButtonText:"Tamam!",
                            customClass:{confirmButton:"btn btn-primary"}
                        }).then((function(e){
                            if(e.isConfirmed){
                                t.querySelector('[name="password"]').value="",
                                t.querySelector('[name="confirm-password"]').value="",
                                o.reset();
                                var r=t.getAttribute("data-kt-redirect-url");
                                r&&(location.href=r)
                            }
                        }))}),1500)):Swal.fire({
                            text:"Bazı Hatalar Tespit Edildi Tekrar Deneyin.",
                            icon:"error",
                            buttonsStyling:!1,
                            confirmButtonText:"Tamam!",
                            customClass:{
                                confirmButton:"btn btn-primary"
                            }})}))})),
                            t.querySelector('input[name="password"]').addEventListener("input",(function(){
                                this.value.length>0&&r.updateFieldStatus("password","NotValidated")
                            }))}}}();
                                                                    
KTUtil.onDOMContentLoaded((function(){
	KTAuthNewPassword.init()
}));