$(function () {



    $(".acceptInstitution").on("click",function (){
      $("#acceptForm input[name=id]").val($(this).val());
      $("#acceptInstitutionModal").modal();
    });

    $(".declineInstitution").on("click",function (){
      $("#deleteForm input[name=id]").val($(this).val());
      $("#declineInstitutionModal").modal();
    });
});