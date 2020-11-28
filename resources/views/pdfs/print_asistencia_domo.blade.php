<html>

<head>
    <style>
        /** 
                Set the margins of the page to 0, so the footer and the header
                can be of the full height and width !
             **/
        @page {
            margin: 0cm 0cm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 2cm;
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 2cm;
        }

        /** Define the header rules **/
        header {
            position: fixed;
            top: 1cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;

            /** Extra personal styles **/
            text-align: center;
            line-height: 0.5cm;
            font-size: 13px;
        }

        table {
          border-collapse: collapse;
          width: 100%;
        }

        table, th, td {
          border: 1px solid black;
          font-size: 11px;
          text-align: left;
          padding: 3px;
        }

        /** Define the footer rules **/
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
        }

        span{
            line-height: 1.8em;
        }

        .logo {
            position: fixed;
            text-align: left;
            margin: 30px, 20px, 0px, 0px;
        }

        .logo_left {
            position: absolute;
            text-align: right;
            margin-left: 470px;
            margin-top: -10px;
        }

        .title-body {
            font-size: 12px;
            font-weight: bold;
        }

        .text-body {
            font-size: 12px;
            font-weight: normal;
            text-align: justify;
        }

        .right {
            text-align: right;
        }

        p {
            line-height: 1.5em;
            font-size: 12px;
            text-align: justify;
        }

        .column {
            float: left;
            width: 50%;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    <!-- Define header and footer blocks before your content -->
    <header>
        <img class="logo" src="{{asset('img/logo.jpg')}}" width="200px" height="100px" />
        <b>ASISTENCIA EMPLEADOS DOMO</b>
    </header>

    <!-- Wrap the content of your PDF inside a main tag -->
    <main style="margin-top: 50px;">
        <div>
            <p>
                NOMBRE DE BUQUE: <label style="color: blue;">{{strtoupper($asignacion->planificacion->buque->nombre)}}</label><br />
                FECHA DE ATRAQUE: <label style="color: blue;">{{date('d-m-Y',strtotime($asignacion->planificacion->fecha_atraque))}}</label><br />
                FECHA DE ASIGNACION: <label style="color: blue;">{{date('d-m-Y',strtotime($detalle[0]->fecha))}}</label>
            </p>
            <div style="margin-top: 20px;">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NOMBRE COMPLETO</th>
                            <th>DPI</th>
                            <th>NUMERO CARNET</th>
                            <th>HORA ENTRADA</th>
                            <th>HORA SALIDA</th>
                            <th>ROL</th>
                            <th>TURNO</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                         @foreach($detalle as $d)
                        <tr>
                            <td style="width:4%">{{ $loop->index + 1}}</td>
                            <td style="width:25%">{{$d->empleado}}</td>
                            <td style="width:10%">{{$d->dpi}}</td>
                            <td>{{$d->carnet}}</td>
                            <td>
                                @if($d->hora_entrada !== null)
                                    <span>{{date('H:i',strtotime($d->hora_entrada))}}</span>
                                @else
                                    <span style="color: red;">sin asistencia</span>
                                @endif
                            </td>
                            <td>
                                @if($d->hora_salida !== null)
                                    <span>{{date('H:i',strtotime($d->hora_salida))}}</span>
                                @else
                                    <span style="color: red;">sin asistencia</span>
                                @endif
                            </td>
                            <td>
                                <span>{{$d->cargo}}</span>
                            </td>
                            <td>
                                <span>{{$d->turno}}</span>
                            </td>
                            <td>
                                @if($d->bloqueado)
                                    <span style="color: red;">- bloqueado</span>
                                @endif
                                @if($d->desbloqueado)
                                    <span style="color: green;"> - desbloqueado</span>
                                @endif
                                
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
            </div>
            <br />
            <br />
            <p>
                <div class="row">
                    <div class="column">
                       <span> Observaciones: ___________________________________________________________<br />__________________________________________________________</span><br />
                    </div>
                    <div class="column" style="margin-left: 50px;">
                        <span>Responsable del Personal: <label style="color: blue;"> Mario Bran Zamora Tel. 32157126 </label></span> <br />
                       <span> Firma y Sello: ______________________________________</span>
                    </div>
                </div>
            </p>
        </div>
    </main>

    <footer>

    </footer>
</body>