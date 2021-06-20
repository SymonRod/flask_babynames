from flask import Flask, redirect, url_for
from flask import Flask, render_template
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from sqlalchemy import Column, Integer, String
from sqlalchemy.orm import declarative_base
from flask import request

Base = declarative_base()


class User(Base):
    __tablename__ = 'users'

    id = Column(Integer, primary_key=True)
    name = Column(String)
    fullname = Column(String)
    nickname = Column(String)

    def __repr__(self):
        return "<User(name='%s', fullname='%s', nickname='%s')>" % (self.name, self.fullname, self.nickname)


app = Flask(__name__, static_url_path='',)

engine = create_engine('sqlite:///database/db.sqlite',
                       echo=True, connect_args={'check_same_thread': False})

Session = sessionmaker()
Session.configure(bind=engine)
session = Session()


@app.route("/newUtente")
def newUtente():
    return render_template("index.html", content="Testing")

# @app.route("/")
# def home():

# 	users = []

# 	for instance in session.query(User).order_by(User.id):
# 		users.append(instance)
# 		print(instance.name)


# 	data = {'users':users}
# 	return render_template("index.html", content="Testing", data=data)

@app.route("/<name>")
def user(name):
    return f"Hello {name}!"


@app.route("/")
def index():
    return render_template("index.html")


if __name__ == "__main__":
    app.run(debug=True)
