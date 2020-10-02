<html>
    <body>
        
        <main>
            <h1>Import error</h1>
            <pre>
                <?php $this->import('../Imports/notexists') ?>
            </pre>
            <h1>Import OK</h1>
            <pre>
                <?php $this->import('../Imports/header') ?>
            </pre>
            <h1>Parameter default (htmlspecialchars)</h1>
            <pre>
                {{ $param }}
            </pre>
            <h1>Parameter especial (no htmlspecialchars)</h1>
            <pre>
                {{!! $param !!}}
            </pre>
            <h1>Parameter default error</h1>
            <pre>
                {{ $paramNoExists }}
            </pre>
            <h1>Parameter especial error</h1>
            <pre>
                {{!! $paramNoExists !!}}
            </pre>

            
        </main>
    </body>
</html>