import moment from 'moment'
export default {

    captureError(r) {
        if (r.response) {
            if (typeof r.response.data.error === 'object') {
                for (let value of Object.values(r.response.data.error)) {
                    toastr.error(value, 'Mensaje')
                }
            } else {
                toastr.error(r.response.data.error, 'error')
                if (r.response.status === 403) {
                    router.push("/")
                }
            }
            return true
        }
        return false
    },

    //funcion para convertir decimales a horas, si no se envia los paramtros opciones devuelve hora completa
    decimalToHour(a, only_hour = false, only_minutes = false) {
        var hrs = parseInt(Number(a))
        var min = Math.round((Number(a) - hrs) * 60)
        hrs < 10 ? hrs = '0' + hrs : hrs
        min < 10 ? min = '0' + min : min
        return only_hour ? hrs : only_minutes ? min : hrs + ':' + min
    },

    getHoursByMinutes(mins) {
        var decimals = mins / 60
        return this.decimalToHour(decimals)
    },

    formatPrice(value, symbol = 'Q') {
        let val = (value / 1).toFixed(2).replace('.', '.')
        return symbol + ' ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    },

    //obtener mes por numero
    getMonthByNumber(number) {
        var months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
        ];
    },

    returnMes(mes) {
        let self = this
        return moment().month(mes - 1).format('MMMM')
    },

    //formatear codigo para tarjeta de reponsabilidades
    formatCode(n, len = 4) {
        return (new Array(len + 1).join('0') + n).slice(-len)
    },

    //obtener full name
    getFullName(data, tercer_nombre = false) {
        Object.keys(data).forEach(function(key) {
            if (data[key] === null) {
                data[key] = '';
            }
        })
        var pn = data.primer_nombre
        var sn = data.segundo_nombre
        var tn = tercer_nombre ? data.tercer_nombre : ''
        var pa = data.primer_apellido
        var sa = data.segundo_apellido
        var name = pn + ' ' + sn + ' ' + tn + ' ' + pa + ' ' + sa
        return name.replace(/\s+/g, " ").replace(/^\s|\s$/g, "");
    },

    //funcion para convertir numeros a letras
    numeroALetras(num) {
        var data = {
            numero: num,
            enteros: Math.floor(num),
            centavos: (((Math.round(num * 100)) - (Math.floor(num) * 100))),
            letrasCentavos: "",
            letrasMonedaPlural: '', //"PESOS", 'Dólares', 'etcs'
            letrasMonedaSingular: '', //"PESO", 'Dólar', 'etc'

            letrasMonedaCentavoPlural: "CENTAVOS",
            letrasMonedaCentavoSingular: "CENTAVO"
        };

        if (data.centavos > 0) {
            data.letrasCentavos = "CON " + (function() {
                if (data.centavos == 1)
                    return this.Millones(data.centavos) + " " + data.letrasMonedaCentavoSingular
                else
                    return this.Millones(data.centavos) + " " + data.letrasMonedaCentavoPlural
            })();
        };

        if (data.enteros == 0)
            return "CERO " + data.letrasMonedaPlural + " " + data.letrasCentavos
        if (data.enteros == 1)
            return this.Millones(data.enteros) + " " + data.letrasMonedaSingular + " " + data.letrasCentavos
        else
            return this.Millones(data.enteros) + " " + data.letrasMonedaPlural + " " + data.letrasCentavos
    },


    Unidades(num) {

        switch (num) {
            case 1:
                return "UN"
            case 2:
                return "DOS"
            case 3:
                return "TRES"
            case 4:
                return "CUATRO"
            case 5:
                return "CINCO"
            case 6:
                return "SEIS"
            case 7:
                return "SIETE"
            case 8:
                return "OCHO"
            case 9:
                return "NUEVE"
        }

        return "";
    },

    Decenas(num) {

        var decena = Math.floor(num / 10);
        var unidad = num - (decena * 10);

        switch (decena) {
            case 1:
                switch (unidad) {
                    case 0:
                        return "DIEZ"
                    case 1:
                        return "ONCE"
                    case 2:
                        return "DOCE"
                    case 3:
                        return "TRECE"
                    case 4:
                        return "CATORCE"
                    case 5:
                        return "QUINCE"
                    default:
                        return "DIECI" + this.Unidades(unidad);
                }
            case 2:
                switch (unidad) {
                    case 0:
                        return "VEINTE"
                    default:
                        return "VEINTE Y " + this.Unidades(unidad)
                }
            case 3:
                return this.DecenasY("TREINTA Y", unidad)
            case 4:
                return this.DecenasY("CUARENTA Y", unidad)
            case 5:
                return this.DecenasY("CINCUENTA Y", unidad)
            case 6:
                return this.DecenasY("SESENTA Y", unidad)
            case 7:
                return this.DecenasY("SETENTA Y", unidad)
            case 8:
                return this.DecenasY("OCHENTA Y", unidad)
            case 9:
                return this.DecenasY("NOVENTA Y", unidad)
            case 0:
                return this.Unidades(unidad)
        }
    },

    DecenasY(strSin, numUnidades) {
        if (numUnidades > 0)
            return strSin + " Y " + this.Unidades(numUnidades)

        return strSin;
    },

    Centenas(num) {
        var centenas = Math.floor(num / 100);
        var decenas = num - (centenas * 100);

        switch (centenas) {
            case 1:
                if (decenas > 0)
                    return "CIENTO " + this.Decenas(decenas)
                return "CIEN";
            case 2:
                return "DOSCIENTOS " + this.Decenas(decenas)
            case 3:
                return "TRESCIENTOS " + this.Decenas(decenas)
            case 4:
                return "CUATROCIENTOS " + this.Decenas(decenas)
            case 5:
                return "QUINIENTOS " + this.Decenas(decenas)
            case 6:
                return "SEISCIENTOS " + this.Decenas(decenas)
            case 7:
                return "SETECIENTOS " + this.Decenas(decenas)
            case 8:
                return "OCHOCIENTOS " + this.Decenas(decenas)
            case 9:
                return "NOVECIENTOS " + this.Decenas(decenas)
        }

        return this.Decenas(decenas);
    },

    Seccion(num, divisor, strSingular, strPlural) {
        var cientos = Math.floor(num / divisor)
        var resto = num - (cientos * divisor)

        var letras = "";

        if (cientos > 0)
            if (cientos > 1)
                letras = this.Centenas(cientos) + " " + strPlural
            else
                letras = strSingular

        if (resto > 0)
            letras += ""

        return letras;
    },

    Miles(num) {
        var divisor = 1000;
        var cientos = Math.floor(num / divisor)
        var resto = num - (cientos * divisor)

        var strMiles = this.Seccion(num, divisor, "UN MIL", "MIL")
        var strCentenas = this.Centenas(resto)

        if (strMiles == "")
            return strCentenas

        return strMiles + " " + strCentenas
    },

    Millones(num) {
        var divisor = 1000000;
        var cientos = Math.floor(num / divisor)
        var resto = num - (cientos * divisor)

        var strMillones = this.Seccion(num, divisor, "UN MILLON DE", "MILLONES DE");
        var strMiles = this.Miles(resto);

        if (strMillones == "")
            return strMiles;

        return strMillones + " " + strMiles;
    },

    getLogo(){
        return 'data:image/png;base64,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'
    },

    getCircleLogo() {
        return 'data:image/png;base64,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'
    }
}