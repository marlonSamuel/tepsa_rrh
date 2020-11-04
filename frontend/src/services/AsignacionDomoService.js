class AsignacionDomoService{
    axios
    baseUrl

    constructor(axios,baseUrl){
        this.axios = axios
        this.baseUrl = `${baseUrl}asignacion_domos`
    }

    getAll(){
        let self = this
        return self.axios.get(`${self.baseUrl}`)
    }

    get(id){
        let self = this
        return self.axios.get(`${self.baseUrl}/${id}`)
    }

    getDetail(id,turno_id,fecha){
        let self = this
        return self.axios.get(`${self.baseUrl}/${id}/${turno_id}/${fecha}`)
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

    //imprimir contrato
    print(id,fecha) {
        let self = this
        return self.axios.get(`${self.baseUrl}_print/${id}/${fecha}`, { responseType: 'blob' });
    }
}

export default AsignacionDomoService