function copyToClipboard(text, el) {
    var copyTest = document.queryCommandSupported('copy');
    var elOriginalText = el.attr('data-original-title');
    if (copyTest === true) {
        var copyTextArea = document.createElement("textarea");
        copyTextArea.value = text;
        document.body.appendChild(copyTextArea);
        copyTextArea.select();
        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'Copied!' : 'Whoops, not copied!';
            el.attr('data-original-title', msg).tooltip('show');
        } catch (err) {
            console.log('Oops, unable to copy');
        }
        document.body.removeChild(copyTextArea);
        el.attr('data-original-title', elOriginalText);
    } else {
        window.prompt("Copy to clipboard: Ctrl+C or Command+C, Enter", text);
    }
}

$(document).ready(function () {
    $('.js-tooltip').tooltip();
    $('.js-copy').click(function () {
        var text = $(this).attr('data-copy');
        var $el = $(this);
        copyToClipboard(text, $el);
    });
    $('.js-input-password-toggle').click(function () {
        var $el = $($(this).attr('for'));
        if ($(this).hasClass('active')) {
            $el.attr('type', 'password');
        } else {
            $el.attr('type', 'text');
        }
    });
    $('.js-show-text').click(function () {
        var el = $(this).attr('for');
        if ($(this).hasClass('active')) {
            $(el + '-asterix').removeClass('d-none');
            $(el + '-plain').addClass('d-none');
        } else {
            $(el + '-asterix').addClass('d-none');
            $(el + '-plain').removeClass('d-none');
        }
    });
    $(document).on('input.textarea', '#decrypted', function () {
        var scrollHeight = $(this).prop('scrollHeight');
        var height = Math.min(scrollHeight, 180) + "px";
        $(this).css('height', height);
    });
});
