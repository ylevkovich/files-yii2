var copyTextBtn = document.querySelector('.js-textcopybtn');
copyTextBtn.addEventListener('click', function(event) {
    // Выборка ссылки с электронной почтой
    var text = document.querySelector('.js-textlink');
    var range = document.createRange();
    range.selectNode(text);
    window.getSelection().addRange(range);

    try {
        // Теперь, когда мы выбрали текст ссылки, выполним команду копирования
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Copy Text command was ' + msg);

        $('.alert-warning').hide();
        $('.btn-warning').hide();
        $('.alert-success').show();
    } catch(err) {
        console.log('Oops, unable to copy');
    }

    // Снятие выделения - ВНИМАНИЕ: вы должны использовать
    // removeRange(range) когда это возможно
    window.getSelection().removeAllRanges();
});