<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

<!------ form process ------>
<?php
// OPTIONS - PLEASE CONFIGURE THESE BEFORE USE!

$yourEmail = "nicholas@ndiesslin.com"; // the email address you wish to receive these mails through
$yourWebsite = "ndiesslin.com"; // the name of your website
$thanksPage = ''; // URL to 'thanks for sending mail' page; leave empty to keep message on the same page 
$maxPoints = 4; // max points a person can hit before it refuses to submit - recommend 4
$requiredFields = "name,email,comments"; // names of the fields you'd like to be required as a minimum, separate each field with a comma


// DO NOT EDIT BELOW HERE
$error_msg = array();
$result = null;

$requiredFields = explode(",", $requiredFields);

function clean($data) {
    $data = trim(stripslashes(strip_tags($data)));
    return $data;
}
function isBot() {
    $bots = array("Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot", "Teoma", "alexa", "froogle", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot", "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz");

    foreach ($bots as $bot)
        if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
            return true;

    if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ")
        return true;
    
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isBot() !== false)
        $error_msg[] = "No bots please! UA reported as: ".$_SERVER['HTTP_USER_AGENT'];
        
    // lets check a few things - not enough to trigger an error on their own, but worth assigning a spam score.. 
    // score quickly adds up therefore allowing genuine users with 'accidental' score through but cutting out real spam :)
    $points = (int)0;
    
    $badwords = array("adult", "beastial", "bestial", "blowjob", "clit", "cum", "cunilingus", "cunillingus", "cunnilingus", "cunt", "ejaculate", "fag", "felatio", "fellatio", "fuck", "fuk", "fuks", "gangbang", "gangbanged", "gangbangs", "hotsex", "hardcode", "jism", "jiz", "orgasim", "orgasims", "orgasm", "orgasms", "phonesex", "phuk", "phuq", "pussies", "pussy", "spunk", "xxx", "viagra", "phentermine", "tramadol", "adipex", "advai", "alprazolam", "ambien", "ambian", "amoxicillin", "antivert", "blackjack", "backgammon", "texas", "holdem", "poker", "carisoprodol", "ciara", "ciprofloxacin", "debt", "dating", "porn", "link=", "voyeur", "content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript");

    foreach ($badwords as $word)
        if (
            strpos(strtolower($_POST['comments']), $word) !== false || 
            strpos(strtolower($_POST['name']), $word) !== false
        )
            $points += 2;
    
    if (strpos($_POST['comments'], "http://") !== false || strpos($_POST['comments'], "www.") !== false)
        $points += 2;
    if (isset($_POST['nojs']))
        $points += 1;
    if (preg_match("/(<.*>)/i", $_POST['comments']))
        $points += 2;
    if (strlen($_POST['name']) < 3)
        $points += 1;
    if (strlen($_POST['comments']) < 15 || strlen($_POST['comments'] > 1500))
        $points += 2;
    if (preg_match("/[bcdfghjklmnpqrstvwxyz]{7,}/i", $_POST['comments']))
        $points += 1;
    // end score assignments

    foreach($requiredFields as $field) {
        trim($_POST[$field]);
        
        if (!isset($_POST[$field]) || empty($_POST[$field]) && array_pop($error_msg) != "Please fill in all the required fields and submit again.\r\n")
            $error_msg[] = "Please fill in all the required fields and submit again.";
    }

    if (!empty($_POST['name']) && !preg_match("/^[a-zA-Z-'\s]*$/", stripslashes($_POST['name'])))
        $error_msg[] = "The name field must not contain special characters.\r\n";
    if (!empty($_POST['email']) && !preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', strtolower($_POST['email'])))
        $error_msg[] = "That is not a valid e-mail address.\r\n";
    if (!empty($_POST['url']) && !preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $_POST['url']))
        $error_msg[] = "Invalid website url.\r\n";
    
    if ($error_msg == NULL && $points <= $maxPoints) {
        $subject = "Automatic Form Email";
        
        $message = "You received this e-mail message through your website: \n\n";
        foreach ($_POST as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $subval) {
                    $message .= ucwords($key) . ": " . clean($subval) . "\r\n";
                }
            } else {
                $message .= ucwords($key) . ": " . clean($val) . "\r\n";
            }
        }
        $message .= "\r\n";
        $message .= 'IP: '.$_SERVER['REMOTE_ADDR']."\r\n";
        $message .= 'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n";
        $message .= 'Points: '.$points;

        if (strstr($_SERVER['SERVER_SOFTWARE'], "Win")) {
            $headers   = "From: $yourEmail\r\n";
            $headers  .= "Reply-To: {$_POST['email']}\r\n";
        } else {
            $headers   = "From: $yourWebsite <$yourEmail>\r\n";
            $headers  .= "Reply-To: {$_POST['email']}\r\n";
        }

        if (mail($yourEmail,$subject,$message,$headers)) {
            if (!empty($thanksPage)) {
                header("Location: $thanksPage");
                exit;
            } else {
                $result = 'Your mail was successfully sent.';
                $disable = true;
            }
        } else {
            $error_msg[] = 'Your mail could not be sent this time. ['.$points.']';
        }
    } else {
        if (empty($error_msg))
            $error_msg[] = 'Your mail looks too much like spam, and could not be sent this time. ['.$points.']';
    }
}
function get_data($var) {
    if (isset($_POST[$var]))
        echo htmlspecialchars($_POST[$var]);
}
?>
<!------ form process end ------>

    <head>
        <meta charset="utf-8">
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge'><![endif]-->
        <title>Nicholas Diesslin | Portfolio Website | Minneapolis, MN</title>
        <meta name="description" content="This site is for the portfolio work of Nicholas Diesslin, a Web Designer from Minneapolis Minnesota. Nicholas specializes in HTML5 CSS3 and graphic design work.">
        <meta name="keywords" content="Nicholas Diesslin, web designer, design, web design, graphic design, website, portfolio site for web design, Portfolio Survival Guide, HTML5, CSS3, javascript">
        <meta name="author" content="Nicholas Diesslin">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="shortcut icon" sizes="196x196" href="196.png">
        <link rel="shortcut icon" sizes="152x152" href="152.png">
        <link rel="apple-touch-icon" sizes="152x152" href="152.png">
        <link rel="apple-touch-icon-precomposed" sizes="152x152" href="152.png">
        <link href='http://fonts.googleapis.com/css?family=Bitter%7CMerriweather+Sans:400,700' rel='stylesheet' type='text/css'>
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/jquery.sidr.light.css">
        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
    </head>
    <body>
        <section id="intro">
            <h1>Nicholas Diesslin, Web Designer</h1>
            <img src="img/logo.svg" alt="Nicholas Diesslin Logo" id="logo"/>
            <a id="nav-button" href="#sidr">&#9776;</a>
            <div id="sidr-right">
                <ul>
                    <li>
                        <a href="#close-sidr" >&#10006;</a>
                    </li>
                    <li>
                        <a href="#intro" >&#9650; top</a>
                    </li>
                    <li>
                        <a href="#about" >&#9660; about</a>
                    </li>
                    <li>
                        <a href="#projects" >&#9660; projects</a>
                    </li>
                    <li>
                        <a href="#contact-tab" >&#9660; contact</a>
                    </li>
                </ul>
            </div>
            <!-- <div id="arrow">&#59228;</div> -->
        </section>
        <section id="about">
            <h2 id="about-text">
                <span id="about-1">My name is <strong id="nicholas">Nicholas Diesslin</strong>, I’m a <strong>web designer</strong> from Minneapolis.</span>
                <br/> 
                 <span id="about-2">Although I enjoy <em>web design</em>, I also enjoy doing <em>graphic design</em>. Typography is my favorite part of design. Art is a crucial aspect of my life, as it inspires most of my creativity. I like making, playing, and listening to music; my favorite instrument to play is the saxophone. I prefer Marvel over D.C., but Superman is my favorite superhero.</span>
            </h2>
        </section>
        <section id="projects">
            <h2 class="title-tabs" id="work-tab">
                Work
            </h2>
            <div id="project-container">
                <div class="project-1">
                    <img src="img/sequence_l.png" alt="Sequencer JS Preview" title="Sequencer.JS"/>
                    <div class="project-text">
                        <h3>Sequencer.JS</h3>
                        <p>Sequencer.JS was created for a motion scripting class. It utilizes processing.js, lowLag.js, dat.gui.js and jquery. The ultimate idea of a sequencer is to sequence through each drum track to play music. This was relatively easy to accomplish by using loops in <em>javascript</em>. The buttons are built out using proccesing.js and the playback is controlled using dat.gui. This project works best using Google Chrome.</p>
                        <a href="http://ndiesslin.com/sequencer/sequencer2.html" target="_blank">View Site</a>
                    </div>
                </div>
                <div class="project-1">
                    <img src="img/portfolio_l.png" alt="Portfolio Survival Guide Preview" title="Portfolio Survival Guide"/>
                    <div class="project-text">
                        <h3>Portfolio Survival Guide</h3>
                        <p>Portfolio Survival Guide was created by a group of students in  a class on E-Learning. The site is to help future students with bulding their <em>portfolio</em>. I was in change of the mobile wireframing for this project, as well as creating content for the <em>web standards</em> and hosting pages.</p>
                        <a href="http://ndiesslin.com/e/" target="_blank">View Site</a>
                        <a href="http://ndiesslin.com/e/wireframes/mobilewireframes.pdf" target="_blank">View Wireframes</a>
                    </div>
                </div>
                <div class="project-1">
                    <img src="img/studio77_l.png" alt="Studio 77 Preview" title="Studio 77 Preview"/>
                    <div class="project-text">
                        <h3>Studio 77</h3>
                        <p>Studio 77 was a project created with <em>HTML5</em> and <em>CSS3</em>. The <em>website</em> is for a made up audio production company Studio 77. I created everything from the <em>design</em> to the actual coding of this <em>website</em>. Studio 77 will be using Sequencer.JS as a promotional item to bring in more traffic to it's <em>website</em>.</p>
                        <a href="http://ndiesslin.com/studio77/" target="_blank">View Site</a>
                    </div>
                </div>
            </div>
        </section>
        <footer>
            <h2 class="title-tabs" id="contact-tab">
                Contact
            </h2>
            <section id="contact">
                <div id="social-icons">
                    <a href="https://twitter.com/ndiesslin" target="_blank" class="social twitter"></a>
                    <a href="http://linkedin.com/pub/nicholas-diesslin/48/294/53b/" target="_blank"  class="social linkedin"></a>
                    <a href="https://www.behance.net/diesslin" target="_blank" class="social behance"></a>
                </div>
                <form method="post" action="<?php echo basename(__FILE__); ?>">
                    <noscript>
                        <input type="hidden" name="nojs" id="nojs" />
                    </noscript>
                    <input class="form-inputs" type="text" required placeholder="name" name="name" id="name" value="<?php get_data("name"); ?>">
                    <input class="form-inputs" type="email" required placeholder="email" name="email" id="email" value="<?php get_data("email"); ?>">
                    <textarea class="form-inputs text-area" required placeholder="message" name="comments" id="message"><?php get_data("comments"); ?></textarea>
                    <input class="submit" type="submit" name="submit" value="send email" <?php if (isset($disable) && $disable === true) echo ' disabled="disabled"'; ?>>
                    <?php
                        if (!empty($error_msg)) {
                            echo '<p class="error">ERROR: '. implode("<br />", $error_msg) . "</p>";
                        }
                        if ($result != NULL) {
                            echo '<p class="success">'. $result . "</p>";
                        }
                    ?>
                </form>
            </section>
        </footer>
        <div id="bottom-nav">
            <ul id="bottom-nav-left">
                <li>
                    Nicholas Diesslin <?php echo date('Y'); ?>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="docs/vCard.vcf" target="_blank">vCard </a>
                </li>
                <li>
                    <a href="#">resume</a>
                </li>
                <li>
                    <a href="#">site documentation</a>
                </li>
            </ul>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

        <!-- ---- analytics ---- -->
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-46191310-2', 'ndiesslin.com');
          ga('send', 'pageview');

        </script>
    </body>
</html>