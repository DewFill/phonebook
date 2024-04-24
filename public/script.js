
function deleteContact(id) {
    let url = location.href + "api/delete/contact"
    let form = new FormData();
    form.append("id", id)

    fetch(url, {
        method: "POST",
        body: form
    })
        .then(json => json.json())
        .then(function (res) {
            if (res.action === "success") {
                document.querySelector(`[data-contactid="${res.data.id}"]`).remove()
            }
        })
}
document.getElementById("addContactForm").addEventListener("submit", function (e) {
    e.preventDefault()
    const form = new FormData(e.target);

    let url = location.href + "api/add/contact"

    fetch(url, {
        method: "POST",
        body: form
    })
        .then(json => json.json())
        .then(function (res) {
            if (res.action === "success") {
                location.reload()
            } else {
                document.getElementById("error-msg").innerText = res.data
                console.log(res.data)
            }
        })
})

function addContact() {
    let url = location.href + "api/add/contact"
    const form = new FormData();
    form.append("id", id)

    fetch(url, {
        method: "POST",
        body: form
    })
        .then(json => json.json())
        .then(function (res) {
            if (res.action === "success") {
                document.querySelector(`[data-contactid="${res.data.id}"]`).remove()
            }
        })

}
