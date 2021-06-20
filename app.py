from flask import Flask, redirect, url_for
from flask import Flask, render_template
from flask import request
from flask import jsonify
from db import session, Nomi, MiPiace


app = Flask(__name__, static_url_path='',)


@app.route("/newUtente")
def newUtente():
    return render_template("index.html", content="Testing")


@app.route("/api/getLikes")
def getLikes():
    try:
        id_nome = request.args["idNome"]
    except Exception as e:
        return "Error"

    likes = {'likes': []}

    for instance in session.query(MiPiace.valore, MiPiace.id_nome).filter(MiPiace.id_nome == id_nome):
        likes['likes'].append({"value": instance.valore})

    return jsonify(likes)


@app.route("/api/getNames")
def getNames():
    try:
        search_name = request.args["name"]
        if search_name == "":
            raise Exception("Empty search are not allowed")
        languages = request.args["language"]
        languages = languages.split(",")

    except Exception as e:
        return "Error"
    nomi = {"nomi": []}

    for instance in session.query(Nomi.nome, Nomi.id_lingua, Nomi.id).filter(Nomi.nome.like(search_name+"%")).order_by(Nomi.id):
        nome = {"nome": {
            "value": instance.nome,
            "language": instance.id_lingua,
            "id": instance.id}}
        nomi["nomi"].append(nome)

    return jsonify(nomi)


@app.route("/")
def index():
    return render_template("index.html")


if __name__ == "__main__":
    app.run(debug=True)
