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

    destroy(data){
        let self = this
        return self.axios.delete(`${self.baseUrl}/${data.id}`)
    }
}

export default DetalleAsignacionService