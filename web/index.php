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
                        <a href="/?action=encrypt" class="nav-link" rel="nofollow">
                            <em class="fas fa-unlock"></em>
                            Encrypt
                        </a>
                    </li>
                    <li class="nav-item<?php if ($page === 'decrypt'): ?> active<?php endif; ?>">
                        <a href="/?action=decrypt" class="nav-link" rel="nofollow">
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
                <?php if ($action === 'status' && !empty($encrypted) && !empty($decrypted) && !empty($secret)): ?>
                    <div class="alert alert-success">
                        <h5 class="alert-heading">
                            <em class="fas fa-fw fa-key"></em> Secret key
                        </h5>
                        <p>
                            <?php echo $secret ?>
                        </p>
                        <h5 class="alert-heading">
                            <em class="fas fa-fw fa-eye"></em> Decrypted string
                        </h5>
                        <p>
                            <?php echo $decrypted ?>
                        </p>
                        <h5 class="alert-heading">
                            <em class="fas fa-fw fa-eye-slash"></em> Encrypted string
                        </h5>
                        <p class="d-inline-block text-truncate w-100">
                            <a href="/?action=decrypt&encrypted=<?php echo $encrypted ?>" rel="nofollow">
                                <?php echo $encrypted ?>
                            </a>
                        </p>
                    </div>
                <?php else: ?>
                    <form method="POST" action="/?action=<?php echo $action ?>" autocomplete="off">
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <em class="fas fa-fw fa-key"></em>
                                    </div>
                                </div>
                                <input type="text" name="secret" class="form-control form-control-lg<?php if (!empty($error)): ?> is-invalid<?php endif; ?>" value="<?php echo $secret ?>" placeholder="Your secret key"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <?php if ($action === 'decrypt'): ?>
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <em class="fas fa-fw fa-eye-slash"></em>
                                        </div>
                                    </div>
                                    <input type="text" name="encrypted" class="form-control form-control-lg<?php if (!empty($error)): ?> is-invalid<?php endif; ?>" value="<?php echo $encrypted ?>" placeholder="Encrypted string to decode">
                                <?php else: ?>
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <em class="fas fa-fw fa-eye"></em>
                                        </div>
                                    </div>
                                    <input name="decrypted" class="form-control form-control-lg<?php if (!empty($error)): ?> is-invalid<?php endif; ?>" value="<?php echo $decrypted ?>" placeholder="Plain text string to encode"/>
                                <? endif; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <em class="fas fa-angle-double-right"></em>
                            Submit
                        </button>
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
    </body>
</html>
