<header>
    <?php
    $threeimgspath = "immagini" . DS . "album";
    ?>
    <div id="velo" >
        <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="8">

            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <?php for ($i = 1; $i < 3; $i++) { ?>
                    <li data-target="#carouselExampleIndicators" data-slide-to="<?= $i ?>"></li>
                <?php } ?>
            </ol> 
            <div class="carousel-caption d-none d-md-block align-top hb">
                <div class="row" align="center" >
                    <img src="<?= $webresourcesDir . 'Testi\icons\hawk_with_half_wing.svg' ?>" 
                         alt="Integrare SVG con il tag image" width="100" height="100">
                    <h3 id="titolo" class="display-4 text-center" aria-hidden="true">Le Storie di Taita</h3>
                </div>
            </div>

            <div class="carousel-inner" align="center">
                <?php $img = getRndImg($threeimgspath) ?>
                <div class="carousel-item active">
                    <img class="d-block img-responsive" src="<?= $img ?>" alt="first slide" height="650" 
                         preserveAspectRatio="xMidYMid slice" >
                </div>
                <?php for ($i = 1; $i < 3; $i++) {
                    $img = getRndImg($threeimgspath) ?>
                    <div class="carousel-item">
                        <img class="d-block img-responsive" src="<?= $img ?>" alt="<?= $i == 1 ? 'second slide' : 'third slide' ?>" 
                             height="650" preserveAspectRatio="xMidYMid slice">
                    </div>
                <?php } ?>         
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>

        </div> <!--finecarousel-->       
    </div>
</header>

<div class="container-lg" lang="it-IT" link="#000080" vlink="#800000" dir="ltr"><p align="center" style="margin-bottom: 0cm; line-height: 100%" >
<article >
<font color="#3465a4"><font size="6" style="font-size: 28pt">Every
</font></font><em><font color="#3465a4"><font size="6" style="font-size: 28pt"><span style="font-style: normal">beetle</span></font></font></em>
<font color="#3465a4"><font size="6" style="font-size: 28pt"> is a
beauty for his mother</font></font></p>
        <div > 
<p style="margin-bottom: 30px; line-height: 100%"><br/></p>

<p style="margin-bottom: 0cm; line-height: 100%"><font size="4" style="font-size: 16pt">Eccoci
qua. </font>
</p>
<p style="margin-bottom: 20px; line-height: 100%"><br/></p>
<p style="margin-bottom: 0cm; line-height: 150%"><font size="4" style="font-size: 16pt">Dopo
tanto lavoro, notti intere trascorse a tentare di capire dove mettere
un tag o un css o a cosa serve una direttiva php o java script, dopo
avere trascorso giornate bellissime, che avresti dovuto sfruttare per
una sana e sudorifera corsa, invece di &lsquo;inseguire&rsquo; il
video dell&rsquo;insegnante che , a velocit&agrave; supersonica, ti
mostra lo sviluppo dei suoi progetti, il blog di Taita &egrave;
pronto per ulteriori passi avanti.</font></p>
<div class="text-center" style="margin-top: 10px;height:260px;">
<p  style="line-height: 100%">
    <img src="<?= $webresourcesDir.'Testi'.DS.'privacy_e_terms'.DS.'presentazione_html_a0322260fc1d85f6.png'?>" name="croach" class="rounded" width="291" height="281" border="0"/>
</p>
</div><br/>

<p style="margin-bottom: 1cm; margin-top:15px;line-height: 150%"><font size="4" style="font-size: 16pt">Come
direbbero a Napoli, &lsquo;<b>ogni scarrafone &egrave; bell  a mamma
soia</b>&rsquo; . <br/>
Ed &egrave; chiaro che molte cose, nella
grafica e nelle funzionalit&agrave; che troverete qui, dovranno essere migliorate, ma
tutto quello che c&rsquo;&egrave; &egrave; il frutto di quanto ho
imparato e ... mi piace. <br/>
E ora credo sia giunto  il momento di  condividerlo.</font></p>
        </div>
    </article>
</div>







