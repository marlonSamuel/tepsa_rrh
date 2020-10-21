class DetalleAsignacionService{
    axios
    baseUrl

    constructor(axios,baseUrl){
        this.axios = axios
        this.baseUrl = `${baseUrl}detalle_asignacion_empleados`
    }

    getAsign(codigo,fecha,turno_id){
        let self = this
        return self.axios.get(`${self.baseUrl}/${codigo}/${fecha}/${turno_id}`)
    }

    getTurnDate(fecha,turno_id){
        let self = this
        console.log(turno_id)
        return self.axios.get(`${self.baseUrl}/${fecha}/${turno_id}`)
    }

    destroy(data){
        let self = this
        return self.axios.delete(`${self.baseUrl}/${data.id}`)
    }

    //imprimir asistencia
    print(id,turno_id,fecha,a=false) {
        let self = this
        return self.axios.get(`${self.baseUrl}_print/${id}/${turno_id}/${fecha}/${a}`, { responseType: 'blob' });
    }
}

export default DetalleAsignacionService