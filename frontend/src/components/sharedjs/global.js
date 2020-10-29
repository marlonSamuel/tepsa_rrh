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

    getLogo() {
        return 'data:image/png;base64,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'
    },

    getCircleLogo() {
        return 'data:image/png;base64,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'
    }
}