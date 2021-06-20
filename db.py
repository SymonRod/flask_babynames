from sqlalchemy import Column, Integer, String, DateTime
from sqlalchemy.orm import declarative_base
from sqlalchemy import create_engine

from sqlalchemy.orm import sessionmaker

Base = declarative_base()
engine = create_engine('sqlite:///database/data.sqlite',
                       echo=True, connect_args={'check_same_thread': False})

Session = sessionmaker()
Session.configure(bind=engine)
session = Session()


class Nomi(Base):
    __tablename__ = 'Nomi'
    id = Column(Integer, primary_key=True)
    nome = Column(String)
    id_lingua = Column(Integer)


class MiPiace(Base):
    __tablename__ = 'MiPiace'
    id = Column(Integer, primary_key=True)
    id_utente = Column(Integer)
    id_nome = Column(Integer)
    date = Column(DateTime)
    valore = Column(Integer)


class Lingue(Base):
    __tablename__ = 'Lingue'
    id = Column(Integer, primary_key=True)
    lingua = Column(String)
    valore = Column(String)


class Dettagli(Base):
    __tablename__ = 'Dettagli'
    id = Column(Integer, primary_key=True)
    name = Column(String)
    fullname = Column(String)
    nickname = Column(String)
