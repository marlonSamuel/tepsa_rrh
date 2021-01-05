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
            font-size: 10px;
            line-height: 0.25cm;
        }

        .mytable {
          border-collapse: collapse;
          width: 100%;
        }

        .mytable, th, td {
          border: 1px solid black;
          padding: 2px;
        }

        .custom_header {
            top: 1cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;

            /** Extra personal styles **/
            text-align: center;
        }

        .logo {
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
            font-weight: bold;
            color: #14105B;
        }

        .text-body {
            font-weight: normal;
            text-align: justify;
        }

        .right {
            text-align: right;
        }

        .column {
            float: left;
            width: 50%;
        }

        .col-10{
            float: left;
            width: 10%;
        }

        .col-25{
            float: left;
            width: 25%;
        }

        .col-50{
            float: left;
            width: 50%;
        }

        .col-75{
            float: left;
            width: 75%;
        }

        .col-100{
            float: left;
            width: 100%;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        .center{
            text-align: center;
        }

        .page_break { page-break-before: always; }
    </style>
</head>

<body>
    @foreach($detalle as $d)
    <div>
        <div class="custom_header">
            <div class="col-10">
                <img style="padding-left: 65px;" class="logo" src="{{asset('img/logo.jpg')}}" width="150px" height="50px" />
            </div>
            <div class="col-75 title-body" style="margin-top: 40px;">
                TEPSA <br />
                TERMINAL ESPECIALIZADAS DEL PACIFICO, S.A <br />
                PUERTO QUETZAL
            </div>
        </div>
        <div class="col-10">
        </div>

        <div class="col-25 title-body" style="padding-left: -35px;">
            <span style="color: red">
                COMPROBANTE DE PAGO
            </span>
        </div>

        <div style="margin-top: 25px; margin-left: -5px;">
            <table class="mytable">
                <tbody>
                    <tr>
                        <td width="80px;">Planilla:</td>
                        <td width="80px" class="title-body">TEPPQ-{{($planilla->quincena)}}-{{($planilla->anio->anio)}}</td>
                        <td width="80px;">Periodo:</td>
                        <td class="title-body" colspan="2">{{date('d M Y', strtotime($planilla->fecha_inicio))}}</td>
                        <td width="80px;">Al</td>
                        <td class="title-body" colspan="2">{{date('d M Y', strtotime($planilla->fecha_fin))}}</td>
                    </tr>
                    <tr>
                        <td>Codigo:</td>
                        <td class="title-body">{{$d['codigo']}}</td>
                        <td>Nombre:</td>
                        <td colspan="5" class="title-body">{{$d['nombre']}}</td>
                    </tr>
                    <tr>
                        <td>Departamento:</td>
                        <td colspan="3" class="title-body">Operaciones</td>
                        <td>Puesto</td>
                        <td colspan="3" class="title-body">{{$d['puesto']}}</td>
                    </tr>
                </tbody>
            </table>

            <table width="100%">
                <tbody>
                    <tr>
                          <td width="290px;" style="border: none; padding-left: -3px;"> 
                                <table width="100%" class="mytable">
                                    <tbody>
                                        <tr style="background: #DDDCDF" class="title-body">
                                            <td colspan="3" style="text-align: center;">INGRESOS</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Salario Base Mensual</td>
                                            <td class="title-body">Q. {{number_format($d['salario'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Bonif. Decreto</td>
                                            <td class="title-body">Q. {{number_format($d['bonificacion_incetivo'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Horas Extras Simples</td>
                                            <td class="title-body">Q. {{number_format($d['hora_extra_simple'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Horas Extras Dobles</td>
                                            <td class="title-body">Q. {{number_format($d['hora_extra_doble'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Otros Ingresos</td>
                                            <td class="title-body">Q. {{number_format($d['otro_ingreso'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Bono 14</td>
                                            <td class="title-body">Q. {{number_format(($d['mes']==7 ? $d['bono_14'] :0),2)}}</td>

                                        </tr>
                                        <tr>
                                            <td colspan="2">Aguinaldo</td>
                                            <td class="title-body">Q. {{number_format(($d['mes']==12 ? $d['bono_14'] :0),2)}}</td>
                                        </tr>
                                        <tr style="background: #DDDCDF" class="title-body">
                                            <td colspan="2">TOTAL INGRESOS</td>
                                            <td>Q.{{number_format($d['salario']+$d['bonificacion_incetivo']+$d['hora_extra_simple']+$d['hora_extra_doble']+$d['otro_ingreso']+($d['mes']==7 ? $d['bono_14'] :0) + ($d['mes']==12 ? $d['bono_14'] :0),2)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                          </td>
                          <td style="border: none" width="115px;">&nbsp;</td>
                          <td width="263px;" style="border: none"> 
                                 <table width="100%" class="mytable">
                                    <tbody>
                                        <tr style="background: #DDDCDF" class="title-body">
                                            <td colspan="2" style="text-align: center;"><span style="color: red">DESCUENTOS</span></td>
                                        </tr>
                                        <tr>
                                            <td>Anticipo de Sueldo</td>
                                            <td>Q. {{number_format($d['anticipo'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>Cuota laboral igss (0.0483)</td>
                                            <td class="title-body">Q. {{number_format($d['igss'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>ISR Laboral (0.05)</td>
                                            <td class="title-body">Q. {{number_format($d['ISR'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>Otros Descuentos</td>
                                            <td class="title-body">Q. {{number_format($d['otro_descuento'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>Seguros</td>
                                            <td class="title-body">Q. 0.00</td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr style="background: #DDDCDF" class="title-body">
                                            <td>TOTAL DESCUENTOS</td>
                                            <td>Q. {{number_format($d['otro_descuento']+$d['ISR']+$d['igss']+$d['anticipo'],2)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                    </tr>
                    <tr>
                          <td width="160px;" style="border: none"> 
                          </td>
                          <td style="border: none; padding-left: -77px; padding-right: -50px; padding-top: -10px;" width="250px;">
                              <table class="mytable" width="100%" class="title-body">
                                  <tbody>
                                      <tr class="title-body">
                                          <td>Liquido a recibir</td>
                                           <td>Q.{{number_format($d['total'],2)}}</td>
                                      </tr>
                                  </tbody>
                              </table>
                          </td>
                          <td width="150px;" style="border: none"> 
                          </td>
                    </tr>
                </tbody>
            
        </table>
        <div style="margin-top: 30px;" class="title-body">
            <span>_____________________________________________________________________________________________________________________________</span><br />
            <table width="100%">
                <tbody>
                    <tr>
                        <td width="50%" style="text-align: center; border: none;">Recibí Conforme (Nombre y Firma)</td>
                        <td width="50%" style="text-align: center; border: none;">No DPI.({{$d['dpi']}})</td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div>
    </div>
    <br /><hr />
        @if (($loop->index+1)%2 == 0 && ($loop->index+1) < count($detalle))
            <div class="page_break"></div>
        @endif
    @endforeach

</body>