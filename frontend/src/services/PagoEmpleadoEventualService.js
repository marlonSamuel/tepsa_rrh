class PagoEmpleadoEventual{
    axios
    baseUrl

    constructor(axios,baseUrl){
        this.axios = axios
        this.baseUrl = `${baseUrl}pago_empleado_eventuals`
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
        return self.axios.put(`${self.baseUrl}/${data.id}`,data)
    }

    destroy(data){
        let self = this
        return self.axios.delete(`${self.baseUrl}/${data.id}`)
    }

    //imprimir contrato
    print(planilla_id,id) {
        let self = this
        return self.axios.get(`${self.baseUrl}_print_boleta/${planilla_id}/${id}`, { responseType: 'blob' });
    }
}

export default PagoEmpleadoEventual