class EmpleadoPrestacionService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = `${baseUrl}empleado_prestaciones`
    }

    getAll() {
        let self = this
        return self.axios.get(`${self.baseUrl}`)
    }
    create(data) {
        let self = this
        return self.axios.post(`${self.baseUrl}`, data)
    }

    destroy(id) {
        let self = this
        return self.axios.delete(`${self.baseUrl}/${data.id}`)
    }

}

export default EmpleadoPrestacionService