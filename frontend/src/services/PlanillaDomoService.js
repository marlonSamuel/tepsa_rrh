class PlanillaDomoService{
    axios
    baseUrl

    constructor(axios,baseUrl){
        this.axios = axios
        this.baseUrl = `${baseUrl}pago_domo_empleados`
    }

    getAll(){
        let self = this
        return self.axios.get(`${self.baseUrl}`)
    }

    get(id){
        let self = this
        return self.axios.get(`${self.baseUrl}/${id}`)
    }

    info(id,option){
        let self = this
        return self.axios.get(`${self.baseUrl}_info/${id}/${option}`)
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

    export(id) {
        let self = this;
        return self.axios.get(`${self.baseUrl}_export/${id}`, { responseType: 'arraybuffer'});
    }

    //imprimir contrato
    print(planilla_id,id) {
        let self = this
        return self.axios.get(`${self.baseUrl}_print_boleta/${planilla_id}/${id}`, { responseType: 'blob' });
    }
}

export default PlanillaDomoService