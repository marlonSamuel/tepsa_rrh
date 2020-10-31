class EmpleadoService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios;
        this.baseUrl = `${baseUrl}empleados`;
    }

    getAll() {
        let self = this;
        return self.axios.get(`${self.baseUrl}`);
    }

    get(id) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/${id}`)
    }

    create(data) {
        console.log(data);
        let self = this
        return self.axios.post(`${self.baseUrl}`, data)
    }

    update(data) {
        let self = this
        console.log(data);
        return self.axios.put(`${self.baseUrl}/${data.idEmpleado}`, data)
    }

    destroy(data) {
        let self = this
        return self.axios.delete(`${self.baseUrl}/${data.idEmpleado}`)
    }
    disabled(data) {
        let self = this;
        return self.axios.post(`${self.baseUrl}_disabled/${data}`);
    }
    getFoto(data) {
        let self = this;
        return self.axios.get(`${self.baseUrl}_foto/${data}`);
    }
}
export default EmpleadoService