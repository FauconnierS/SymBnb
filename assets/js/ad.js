$('#add-image').click(function () {
    const index = +$('#widgets_counter').val();
    console.log(index);
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

    $('#ad_images').append(tmpl);

    $('#widgets_counter').val(index + 1);

    handleDeleteButtons();

});

function handleDeleteButtons(){
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target ;
        $(target).remove();
    });

}

function udpdateCounter() {
    const count = +$('#ad_images div.form-group').length;

    $('#widgets_counter').val(count);
}

udpdateCounter();
handleDeleteButtons();