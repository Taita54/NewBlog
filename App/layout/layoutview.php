<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#712cf9">
    <meta name="viewport" content="width=device-width, initial-scale=10,shrinc-to-fit=yes">
    <meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />
    <title>DrillSeq</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/public/css/style.css">
    <!-- <link rel="stylesheet" href="<!?php echo $cardSheet; ?>" -->
    <!-- <link rel="stylesheet" href="/public/css/tableInsideCard.css ?>" -->

    <link rel="icon" type="image/x-icon" href="/resources/images/icons/favicon.ico">
</head>

<body>
    <header id="header" role="banner">
        <nav class="navbar navbar-menu navbar-expand-sm navbar-light fixed-top border-bottom box-shadow">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">DrillSeq<img src="<?= $logo ?>" width="40" height="40" alt="Logo" /></a>
                <div class="collapse navbar-collapse " id="navbarCollapse">
                    <ul class="navbar-nav me-auto mb-2 mb-md-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ship fa-1x mr-2 fa-item-ico"></i>
                                Naviga nel sito
                            </a>
                            <ul class="dropdown-menu">
                                <?php
                                foreach ($titles as $k => $item) {
                                    if (is_array($item)) {
                                        $dis = $item[2] === '-' ? 'disabled' : '';
                                ?>
                                        <li>
                                            <a class="dropdown-item nav-link text-dark" href="<?= $dis ? '#' : $item[1] ?>" onclick="setStartMenu('<?= $item[4] ?>')">
                                                <?= !empty($item[3]) ? '<i class="' . $item[3][0] . '" style="' . $item[3][1] . '"></i>' : ''; ?>
                                                <?= $dis ? str_repeat($item[0], 20) : $item[0] ?>
                                            </a>
                                        </li>
                                <?php
                                    }
                                }
                                ?>
                            </ul>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link text-dark" onclick="requiredData()" href="/constructionPage">
                                <i class="fa-solid fa-earth-europe fa-1x mr-2 fa-item-ico"></i>
                                Dove siamo
                            </a>
                        </li>
                        <?php if ($isUserLogged) {
                            include_once  'partials'  . DS . '_menu' . $curUserRole . '.php';
                        } ?>
                    </ul>
                    <?php if ($isUserLogged) : ?>
                        <ul class="navbar-nav flex-row justify-content-between ml-auto">
                            <li class="nav-item dropdown" style="padding: -20px;">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-lock-open fa-item-ico"></i>
                                    User/Exit
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-item nav-link" style="font-size:small">
                                        <i class="fa-solid fa-hammer" style="color: #3f63a7;"></i>
                                        <?= $curUserName ?> ( <?= $curUserRole ?>)
                                    </li>
                                    <li class="dropdown-item nav-link" style="font-size:small">
                                        <a class="text-dark" href="/auth/changePassword">
                                            <i class="fa-solid fa-key" style="color: #3f63a7;"></i>
                                            Reset Password
                                        </a>
                                    </li>
                                    <li class="dropdown-item nav-link" style="font-size:small">
                                        <a class="text-dark" href="/auth/changeValue/phoneNumber">
                                            <i class="fa-solid fa-phone" style="color: #3f63a7;"></i>
                                            Change Phone
                                        </a>
                                    </li>
                                    <li class="dropdown-item nav-link" style="font-size:small">
                                        <a class="text-dark" href="/auth/changeValue/twoFactor" disabled>
                                            <i class="fa-brands fa-cloudversify" style="color: #3f63a7;"></i>
                                            Enable two factor request
                                        </a>
                                    </li>
                                    <li class="dropdown-item nav-link" style="font-size:small">
                                        <a class="text-dark" href="/auth/changeValue/reqTerms" disabled>
                                            <i class="fa-regular fa-bell" style="color: #3f63a7;"></i>
                                            Disable Required Terms
                                        </a>
                                    </li>
                                    <li class="dropdown-item nav-link" style="font-size:small">
                                        <a class="text-dark" href="/auth/changeValue/optTerms" disabled>
                                            <i class="fa-brands fa-nfc-directional" style="color: #3f63a7;"></i>
                                            Enable Optional Terms
                                        </a>
                                    </li>
                                    <li class="dropdown-item nav-link" style="font-size:small">
                                        <a class="text-dark" href="/auth/changeValue/updateAvatar" disabled>
                                            <i class="fa-solid fa-user-astronaut" style="color: #3f63a7;"></i>
                                            Change Avatar
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li class="dropdown-item nav-link" style="font-size:small">
                                        <form class="form" role="form" method="post" action="/auth/logout">
                                            <input type="hidden" name="action" value="logout" />
                                            <button class="btn btn-lg btn-outline-primary">
                                                <i class="fa-solid fa-right-from-bracket"></i>
                                                LOGOUT
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>

                        </ul>

                    <?php else : ?>
                        <ul class="navbar-nav flex-row justify-content-between ml-auto">
                            <li class="nav-item mr-1 px-1 py-2">
                                <a href="/auth/signin" class="btn btn-lg btn-success">
                                    <i class="fa-sharp fa-solid fa-file-signature"></i>
                                    SIGN IN
                                </a>
                            </li>
                            <li class="nav-item  px-1 py-2">
                                <a href="/auth/login" class="btn btn-lg btn-outline-primary">
                                    <i class="fa-sharp fa-solid fa-right-to-bracket"></i>
                                    LOGIN
                                </a>
                            </li>

                        </ul>
                    <?php endif; ?>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
    </header>
    <main>

        <div class="content">
            <?= $data['content'] ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="reg-modal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modal-title"></h1>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body" id="modalBodyContent">
                        <!-- <p>'qui vedrai i contenuti'</p> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <footer class="fixed-footer foot-container border-top  text-muted">
        <div>
            contatti: <email>
                <?= $this->orgParam->email[0] . ' - ' . $this->orgParam->baseMail[0] ?>
            </email>
        </div>
        <div>
            <a class="legalBtn" href="#" data-id="1" id="legal1">Privacy</a>
        </div>
        <div>
            <a class="legalBtn" href="#" data-id="2" id="legal2">Cookies Policy</a>
        </div>
        <!-- <div >
            <a class="legalBtn" href="#" data-id="3" id="legal3">Guida</a>
        </div>
        <div >
            <a class="legalBtn" href="#" data-id="4" id="legal4">Consenso</a>
        </div>
        <div >
            <a class="legalBtn" href="#" data-id="5" id="legal5">Cookies advertise</a>
        </div>
        <div >
            <a class="legalBtn" href="#" data-id="6" id="legal6">Informativa</a>
        </div>
        <div >
            <a class="legalBtn" href="#" data-id="7" id="legal7">Termini opzionali</a>
        </div> -->
        <div>
            &copy; 2021-<?= date('Y') ?>- DrillSeq - GdMSoftAndPict
        </div>

    </footer>

    <script src="https://kit.fontawesome.com/c855a646b9.js" crossorigin="anonymous" defer></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script src="/public/js/scripts.js"></script>
    <script src="/public/js/cookiechoices.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(event) {
            cookieChoices.showCookieConsentDialog('',
                'Chiudi', 'Maggiori Informazioni', '');
        });
    </script>
    <script>
        $(function() {
            $(message).fadeOut(8000);
        });
    </script>
    <script>
        // Seleziona tutti gli elementi con la classe 'legalBtn'
        const legalButtons = document.querySelectorAll('.legalBtn');

        // Aggiungi un gestore di eventi a ciascun bottone
        legalButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Previene il comportamento predefinito del link

                const legalId = this.getAttribute('data-id'); // Ottieni l'ID dal data attribute

                // Esegui una richiesta AJAX per ottenere i dati
                fetch(`/legal/${legalId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Aggiorna il titolo e il contenuto della modale
                        document.getElementById('modal-title').innerText = data.title;
                        document.getElementById('modalBodyContent').innerHTML = data.html;

                        // Mostra la modale
                        var modal = new bootstrap.Modal(document.getElementById('reg-modal'));
                        modal.show();
                    })
                    .catch(error => console.error('Errore:', error));
            });
        });
    </script>
    <script src="/public/js/getRetHereValue.js"></script>
</body>

</html>