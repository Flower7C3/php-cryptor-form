<?php
require_once '../app/app.php';
?>
<!doctype html>
<html class="h-100">
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="googlebot" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <title>Hello, world!</title>
    </head>
    <body class="d-flex flex-column h-100">
        <nav class="navbar navbar-expand-sm navbar-light bg-light mb-4">
            <div class="navbar-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item<?php if ($page === 'encrypt'): ?> active<?php endif; ?>">
                        <a href="<?php printf($config['form_url'], 'encrypt'); ?>" class="nav-link" rel="nofollow">
                            <em class="fas fa-unlock"></em>
                            Encrypt
                        </a>
                    </li>
                    <li class="nav-item<?php if ($page === 'decrypt'): ?> active<?php endif; ?>">
                        <a href="<?php printf($config['form_url'], 'decrypt'); ?>" class="nav-link" rel="nofollow">
                            <em class="fas fa-lock"></em>
                            Decrypt
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a href="https://github.com/Flower7C3/php-cryptor-form" class="nav-link">
                            <em class="fab fa-github"></em>
                            <span class="d-lg-none d-md-none d-sm-none">Source code</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <main role="main" class="flex-shrink-0">
            <div class="container">
                <?php if ($page === 'status' && !empty($encrypted) && !empty($decrypted) && !empty($secret)): ?>
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-2">
                                    <em class="fas fa-fw fa-key"></em> Secret key
                                </h5>
                                <div class="btn-group mb-2">
                                    <div class="btn btn-primary btn-sm js-tooltip js-copy" data-toggle="tooltip" data-placement="left" data-copy="<?php echo $secret ?>" title="Copy secret key to clipboard">
                                        <em class="fas fa-copy"></em>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-1"><?php echo $secret ?></p>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">
                                    <em class="fas fa-fw fa-eye"></em> Decrypted string
                                </h5>
                            </div>
                            <p class="mb-1"><?php echo nl2br($decrypted) ?></p>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-2">
                                    <em class="fas fa-fw fa-eye-slash"></em> Encrypted string
                                </h5>
                                <div class="btn-group mb-2">
                                    <div class="btn btn-primary btn-sm js-tooltip js-copy" data-toggle="tooltip" data-placement="left" data-copy="<?php echo $encrypted ?>" title="Copy entrypted string to clipboard">
                                        <em class="fas fa-copy"></em>
                                    </div>
                                    <a href="<?php printf($config['share_url'], $encrypted); ?>" rel="nofollow" class="btn btn-secondary btn-sm" data-toggle="tooltip" data-placement="left" title="Open share link with encrypted string">
                                        <em class="fas fa-link"></em>
                                    </a>
                                </div>
                            </div>
                            <p class="d-inline-block text-truncate w-100 mb-1"><?php echo $encrypted ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <form method="POST" action="<?php printf($config['form_url'], $action); ?>" autocomplete="off">
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <abbr class="input-group-text" title="Your secret key">
                                        <em class="fas fa-fw fa-key"></em>
                                    </abbr>
                                </div>
                                <input type="text" name="secret" class="form-control form-control-lg<?php if (!empty($invalid['secret'])): ?> is-invalid<?php endif; ?>" value="<?php echo $secret ?>" placeholder="Your secret key"/>
                                <?php if (!empty($invalid['secret'])): ?>
                                    <?php foreach ($invalid['secret'] as $feedback): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $feedback ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <?php if ($page === 'decrypt'): ?>
                                    <div class="input-group-prepend">
                                        <abbr class="input-group-text" title="Encrypted string to decode">
                                            <em class="fas fa-fw fa-eye-slash"></em>
                                        </abbr>
                                    </div>
                                    <input type="text" name="encrypted" class="form-control form-control-lg<?php if (!empty($invalid['encrypted'])): ?> is-invalid<?php endif; ?>" value="<?php echo $encrypted ?>" placeholder="Encrypted string to decode"/>
                                    <?php if (!empty($invalid['encrypted'])): ?>
                                        <?php foreach ($invalid['encrypted'] as $feedback): ?>
                                            <div class="invalid-feedback">
                                                <?php echo $feedback ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="input-group-prepend">
                                        <abbr class="input-group-text" title="Plain text string to encode">
                                            <em class="fas fa-fw fa-eye" style="align-self:self-start;margin-top:9px;"></em>
                                        </abbr>
                                    </div>
                                    <textarea rows="1" name="decrypted" id="decrypted" class="form-control form-control-lg<?php if (!empty($invalid['decrypted'])): ?> is-invalid<?php endif; ?>" style="resize:none" placeholder="Plain text string to encode"><?php echo $decrypted ?></textarea>
                                    <?php if (!empty($invalid['decrypted'])): ?>
                                        <?php foreach ($invalid['decrypted'] as $feedback): ?>
                                            <div class="invalid-feedback">
                                                <?php echo $feedback ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <? endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <em class="fas fa-angle-double-right"></em>
                                Submit
                            </button>
                        </div>
                        <?php if (!empty($invalid['form'])): ?>
                            <?php foreach ($invalid['form'] as $feedback): ?>
                                <div class="invalid-feedback d-block">
                                    <?php echo $feedback ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </form>
                <? endif; ?>
            </div>
        </main>
        <footer class="footer mt-auto py-3">
            <div class="container">
                <small class="text-muted">
                    &copy; <a href="https://Kwiatek.pro">Kwiatek.pro</a>
                </small>
            </div>
        </footer>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
        <script>
            <?php if ($page === 'encrypt'): ?>
            var textarea = document.getElementById("decrypted");
            var limit = 180;
            textarea.oninput = function () {
                textarea.style.height = "";
                console.log(textarea.scrollHeight)
                textarea.style.height = Math.min(textarea.scrollHeight, limit) + "px";
            };
            <?php endif; ?>
            <?php if ($page === 'status'): ?>
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
                    var el = $(this);
                    copyToClipboard(text, el);
                });
            });
            <?php endif; ?>
        </script>
    </body>
</html>
