<!DOCTYPE html>
<html>
    <head>
        <title>XDebug Flame Graph</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2" />
        <style>
            label {cursor: pointer;}
            svg{width:100%;}
        </style>
    </head>
    <body>
        <?php
          // if (!empty($_GET)) { echo '_GET:'; var_dump($_GET); }
          // if (!empty($_POST)) { echo '_POST:'; var_dump($_POST); }
          if (!empty($_REQUEST)) { echo '_REQUEST:'; var_dump($_REQUEST); }
          // if (!empty($_SERVER)) { echo '_SERVER:'; var_dump($_SERVER); }
        ?>
        <h1>XDebug Flame Graph</h1>
        <form method="POST">
            <label for="file">File:</label>
            <select name="file">
                <?php
                    $dir = ini_get('xdebug.output_dir');
                    $files = glob("$dir/*.xt");

                    foreach ($files as $file) {
                        $checked = ($file == $_REQUEST['file']) ? 'selected="selected"' : '';
                        echo '<option value="' . htmlspecialchars($file) . '" '.$checked.'>' . htmlspecialchars(basename($file)) . '</option>';
                    }
                ?>
            </select>
            <button type="submit">Load</button>
            <br/>
            <p>Files from <code>xdebug.output_dir = <?php echo htmlspecialchars($dir) ?></code></p>
        </form>
        <?php
            if (!empty($_REQUEST['file'])) {
                $file = $_REQUEST['file'];

                if (!file_exists($file)) { echo "input file does not exist"; return; }
                if (!is_readable($file)) { echo "cannot read input file"; return; }

                // passthru(__DIR__.'/brendangregg/FlameGraph/stackcollapse-xdebug.php '.$file.' | '.__DIR__.'/brendangregg/FlameGraph/flamegraph.pl');
                passthru(__DIR__.'/brendangregg/FlameGraph/stackcollapse-xdebug.php');
                // passthru(__DIR__.'/brendangregg/FlameGraph/stackcollapse-xdebug.php '.$file.' > /tmp/xdebug_output.fg');
            }
        ?>
    </body>
</html>