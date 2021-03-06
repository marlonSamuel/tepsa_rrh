class TurnoService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = `${baseUrl}turnos`
    }

    getAll() {
        let self = this
        return self.axios.get(`${self.baseUrl}`)
    }

    get(id) {
        let self = this
        return self.axios.get(`${self.baseUrl}/${id}`)
    }

    getCargos(id) {
        let self = this
        return self.axios.get(`${self.baseUrl}/${id}/cargos`)
    }

    create(data) {
        let self = this
        return self.axios.post(`${self.baseUrl}`, data)
    }
    createCargoTurno(id, data) {
        let self = this;
        return self.axios.post(`${self.baseUrl}/${id}/cargos`, data);
    }

    update(data) {
        let self = this
        return self.axios.put(`${self.baseUrl}/${data.id}`, data)
    }

    destroy(data) {
        let self = this
        return self.axios.delete(`${self.baseUrl}/${data.id}`)
    }
    startQuincena() {
        let self = this
        return self.axios.get(`${self.baseUrl}_start_quincena`)
    }
}

export default TurnoService