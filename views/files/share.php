<div class="alert alert-success">This code has already copied in your buffer</div>
<div class="well well-lg"><a class="js-emaillink" href="1">1</a></div>


<!---->
<!--<p><button class="js-emailcopybtn"><img src="./images/copy-icon.png" /></button></p>-->

<script>
//    var copyEmailBtn = document.querySelector('.js-emailcopybtn');
//    copyEmailBtn.addEventListener('click', function(event) {
        // Выборка ссылки с электронной почтой
        var emailLink = document.querySelector('.js-emaillink');
        var range = document.createRange();
        range.selectNode(emailLink);
        window.getSelection().addRange(range);

        try {
            // Теперь, когда мы выбрали текст ссылки, выполним команду копирования
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
            console.log('Copy email command was ' + msg);
        } catch(err) {
            console.log('Oops, unable to copy');
        }

        // Снятие выделения - ВНИМАНИЕ: вы должны использовать
        // removeRange(range) когда это возможно
        window.getSelection().removeAllRanges();
//    });
</script>