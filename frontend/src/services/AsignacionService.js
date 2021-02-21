class AsignacionService{
    axios
    baseUrl

    constructor(axios,baseUrl){
        this.axios = axios
        this.baseUrl = `${baseUrl}asignacion_empleados`
    }

    getAll(){
        let self = this
        return self.axios.get(`${self.baseUrl}`)
    }

    get(id){
        let self = this
        return self.axios.get(`${self.baseUrl}/${id}`)
    }

    search(date,buque_id){
        let self = this
        return self.axios.get(`${self.baseUrl}_asignacion/${date}/${buque_id}`)
    }

    getDomos(id){
        let self = this
        return self.axios.get(`${self.baseUrl}_domos/${id}`)
    }

    getDetail(id,turno_id,fecha){
        let self = this
        return self.axios.get(`${self.baseUrl}/${id}/${turno_id}/${fecha}`)
    }

    getByEmpleado(id,empleado_id){
        let self = this
        return self.axios.get(`${self.baseUrl}_get/${id}/${empleado_id}`)
    }

    create(data){
        let self = this
        return self.axios.post(`${self.baseUrl}`,data)
    }

    update(data){
        let self = this
        return self.axios.put(`${self.baseUrl}/${data.id}`,data)
    }

    destroy(data){
        let self = this
        return self.axios.delete(`${self.baseUrl}/${data.id}`)
    }

    releaseCards(data){
        let self = this
        return self.axios.put(`${self.baseUrl}_release/${data.id}`)
    }

    //imprimir contrato
    print(id,turno_id,fecha,empleado_id) {
        let self = this
        if (empleado_id == undefined)
            empleado_id = 0
        return self.axios.get(`${self.baseUrl}_print/${id}/${turno_id}/${fecha}/${empleado_id}`, { responseType: 'blob' });
    }
}

export default AsignacionService