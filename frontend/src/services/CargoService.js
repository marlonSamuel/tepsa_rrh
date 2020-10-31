class CargoService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios;
        this.baseUrl = `${baseUrl}cargos`;
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
        let self = this
        return self.axios.post(`${self.baseUrl}`, data)
    }

    update(data) {
        let self = this
        return self.axios.put(`${self.baseUrl}/${data.idCargo}`, data)
    }

    destroy(data) {
        let self = this
        return self.axios.delete(`${self.baseUrl}/${data.idCargo}`)
    }
    disabled(data) {
        let self = this;
        return self.axios.post(`${self.baseUrl}_disabled/${data.idCargo}`);
    }
}
export default CargoService