import random
import string
from app_secret import secret
from flask import Flask, render_template, request, jsonify, url_for, redirect
from flask import session as flask_session
from sqlalchemy.sql.functions import user
from db import session, Nomi, MiPiace, UserTest
from passlib.hash import pbkdf2_sha256


app = Flask(__name__, static_url_path='',)


def id_generator(size=6, chars=string.ascii_uppercase + string.ascii_lowercase + string.digits):
    return ''.join(random.choice(chars) for _ in range(size))


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


@app.route("/login")
def verifyUser():
    try:
        username = request.args["username"]
        passwd = request.args["password"]
    except Exception as e:
        return "Error"

    for user in session.query(UserTest.nickname, UserTest.password, UserTest.salt).filter(UserTest.nickname == username):
        passwd = pbkdf2_sha256.hash(
            passwd, rounds=200000, salt=user.salt.encode())

        if passwd == user.password:
            flask_session['logged'] = True
            flask_session['nickname'] = user.nickname
            return redirect("/")

    return "Username o password incorretti!"


@app.route("/addUser")
def addUser():
    try:
        username = request.args["username"]
        passwd = request.args["password"]
        if("salt" in request.args.keys()):
            salt = request.args["salt"]
        else:
            salt = id_generator(40)
    except Exception as e:
        return "Error"

    for user in session.query(UserTest.nickname).filter(UserTest.nickname == username):
        return 'Username alredy exits!'

    passwd = pbkdf2_sha256.hash(passwd, rounds=200000, salt=salt.encode())

    user = UserTest(nickname=username, password=passwd, salt=salt)

    session.add(user)

    session.commit()

    return "User added!"


# @app.route("/testhash")
# def testhash():
#     try:
#         passwd = request.args["passwd"]
#         if("salt" in request.args.keys()):
#             salt = request.args["salt"]
#         else:
#             salt = id_generator(40)
#     except Exception as e:
#         return "Error"
#     passwd = pbkdf2_sha256.hash(passwd, rounds=200000, salt=salt.encode())
#     return "salt: "+salt+" | pass: "+passwd


@app.route("/logout")
def logout():
    vars = []
    for key in flask_session:
        vars.append(key)

    for key in vars:
        flask_session.pop(key)

    return redirect("/")


@ app.route("/api/getNames")
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


@ app.route("/")
def index():
    return render_template("index.html", session=flask_session)


if __name__ == "__main__":
    app.secret_key = secret
    app.run(debug=True)
