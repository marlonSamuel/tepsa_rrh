class AsistenciaDomoService{
    axios
    baseUrl

    constructor(axios,baseUrl){
        this.axios = axios
        this.baseUrl = `${baseUrl}asistencia_domos`
    }

    getAll(){
        let self = this
        return self.axios.get(`${self.baseUrl}`)
    }

    get(id){
        let self = this
        return self.axios.get(`${self.baseUrl}/${id}`)
    }

    create(data){
        let self = this
        return self.axios.post(`${self.baseUrl}`,data)
    }

    update(data){
        let self = this
        console.log(data)
        return self.axios.put(`${self.baseUrl}/${data.id}`,data)
    }

    desbloquear(data){
        let self = this
        return self.axios.put(`${self.baseUrl}_desbloquear/${data.id}`,data)
    }

    destroy(data){
        let self = this
        return self.axios.delete(`${self.baseUrl}/${data.id}`)
    }

    //imprimir contrato
    print(id,fecha,turno) {
        let self = this
        return self.axios.get(`${self.baseUrl}_print_asistencia/${id}/${fecha}/${turno}`, { responseType: 'blob' });
    }
}

export default AsistenciaDomoService