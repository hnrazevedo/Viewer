Content imported

<div style="border:solid 1px black">
    <h1>Object Parameter</h1>
    <pre>
        {{ $obj.name }}
    </pre>
    <h1>Object Parameter dynamic</h1>
    <pre>
        {{ $obj.email }}
    </pre>
    <h1>Object Parameter array</h1>
    <pre>
        {{ $obj.values.1 }}
    </pre>
    <h1>Sub-object Parameter</h1>
    <pre>
        {{ $obj.address.nation }}
    </pre>
    <h1>Object Parameter error</h1>
    <pre>
        {{ $obj.notExists }}
    </pre>
</div>