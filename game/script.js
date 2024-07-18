class Player {
    constructor(game){
        this.game = game;
        //dimensioni del giocatore
        this.width = 100;
        this.height = 100;

        //il giocatore viene posizionato inizialmente al centro
        this.x = this.game.width / 2 - this.width / 2;
        this.y = this.game.height - this.height;

        this.speed = 10; //velocità di moviemento del giocatore
        this.lives = 3;
        this.image = document.getElementById("player");     //sprite corrispondente al giocatore
    }

    //funzione per disegnare il giocatore nella posizione corrente
    draw(context){
        context.drawImage(this.image, 0, 0, 577, 529, this.x, this.y, this.width, this.height);
    }

    //aggiorna la posizione del giocatore in base ai tasti che sono attualmente premuti
    update(){
        if(this.game.keys.indexOf('a') > -1 || this.game.keys.indexOf('A') > -1) this.x -= this.speed;
        if(this.game.keys.indexOf('d') > -1 || this.game.keys.indexOf('D') > -1) this.x += this.speed;
        
        //permette al giocatore di muoversi fino al bordo in modo da poter colpire eventiali nemici
        if(this.x < -this.width * 0.5) this.x = -this.width * 0.5;
        else if(this.x > this.game.width - this.width * 0.5) this.x = this.game.width - this.width * 0.5;
    }

    //funzione per sparare un proiettile
    shoot(){
        const projectile = this.game.getProjectile();   //ricerca di un proiettile libero
        if (projectile) projectile.fire(this.x + this.width * 0.5, this.y);
    }
}

class Projectile {
    constructor(){
        //dimensioni e posizione iniziale del proiettile
        this.width = 20;
        this.height = 35;
        this.x = 0;
        this.y = 0;

        this.speed = 20; //velocità di moviemento del proiettile
        this.free = true;   //il proiettile è libero (non è ancora stato sparato)
        this.image = document.getElementById('laser');  //immagine corrispondente al proiettile
    }

    draw(context){
        if(!this.free){     //si disegnano solo i proiettili sparati
            context.drawImage(this.image, 0, 0, 81, 126, this.x - this.width * 0.5, this.y, this.width, this.height);
        }
    }

    //funzione per l'aggionamento della posizione di un proiettile
    update(){
        if(!this.free){ //vengono aggiornati solo i proiettili sparati
            this.y -= this.speed;
            if (this.y < -this.height){ //proiettile non più visibile
                this.reset();
            }
        }
    }

    //funzione che spara un proiettile (x, y) coordinate centro del giocatore
    fire(x, y){
        this.free = false;
        this.x = x;
        this.y = y;
    }

    //nel momento in cui il proiettile non è più visibile viene impostato nuovamente come libero
    reset(){
        this.free = true;
    }

}

class Enemy {
    constructor(game, positionX, positionY){
        this.game = game;

        //dimensionamento e posizionamento iniziale del nemico
        this.width = this.game.enemySize;
        this.height = this.game.enemySize;
        this.x = 0;
        this.y = 0;

        //posizione del nemico nell'ondata
        this.positionX = positionX;
        this.positionY = positionY;

        this.eliminated = false;
        this.explosion = false;

        this.imageIndex = (Math.random() < 0.5) ? 0 : 1;    //scelta tra due tipi di nemici
        this.image = document.getElementsByClassName('enemies')[this.imageIndex];   //spritesheet contenente le animazioni del nemico scelto
        this.imageFrame = 0;    //frame animazione corrente
        
        //posizione del frame all'interno dello spritesheet e dimensione di ogni frame
        this.imageX = 0;
        this.imageY = 0;
        this.imageWidth = 49;
        this.imageHeight = 42;

        this.imageExplosion = document.getElementById('explosion');     //spritesheet esplosioni
        this.explosionFrame = 0;    //frame esplosione corrente

        //posizione del frame all'interno dello spritesheet
        this.explosionX = 0;
        this.explosionY = Math.floor(Math.random() * 3);    //scelta tra 3 diversi tipi di esplosione

        //dimensione di ogni frame dell'esplosione
        this.explosionWidth = 300;
        this.explosionHeight = 300;
    }

    //disegna il nemico nella posizione corretta
    draw(context){
        if(!this.explosion){    //il nemico non è stato eliminato
            context.drawImage(this.image, this.imageX + (Math.floor(this.imageFrame / 3) * this.imageWidth), this.imageY, this.imageWidth, this.imageHeight, this.x, this.y, this.width, this.height);
            this.imageFrame++;
            if(this.imageFrame === 48)
                this.imageFrame = 0;
        }
        else{   //viene disegnata l'esplosione frame per frame, una volta terminata il nemico viene segnato come eliminato
            context.drawImage(this.imageExplosion, this.explosionX + (this.explosionWidth * this.explosionFrame), this.explosionY * this.explosionHeight, this.explosionWidth, this.explosionHeight, this.x, this.y, this.width, this.height);
            this.explosionFrame++;
            if(this.explosionFrame === 22)
                this.eliminated = true;
        }
    }

    //funzione che effettua l'aggiornamento della posizione del nemico e gestione di eventuali collisioni
    update(x, y){
        this.x = x + this.positionX;
        this.y = y + this.positionY;

        //controllo se il nemico è stato colpito da un proiettile
        this.game.projectiles.forEach(projectile => {
            if(!projectile.free && this.game.collisionDetection(this, projectile) && !this.explosion){
                this.explosion = true;  //se il nemico è stato colpito inizia l'animazione di esplosione
                projectile.reset();
                if(!this.game.gameOver) this.game.score++;
            }
        });

        //controllo se il nemico ha superato la linea del giocatore (la partita è finita)
        if(this.y + this.height > this.game.height){
            this.game.gameOver = true;
            this.eliminated = true;     //il nemico non essendo più visibile viene eliminato
        }

        //controllo eventuale collisione tra il giocatore ed un nemico
        if(this.game.collisionDetection(this, this.game.player)){
            this.eliminated = true;     //in caso di collisione il nemico viene elimiato
            if(!this.game.gameOver){
                this.game.player.lives--;   //viene tolta una vita al giocatore
                if(this.game.score > 0)
                    this.game.score--;      //il punteggio viene decrementato di 1 fino ad un minimo di 0
            }
            if(this.game.player.lives === 0)    //se il giocatore arriva a 0 vite rimanenti la partita termina
                this.game.gameOver = true;
        }
    }
}

class Wave {
    constructor(game){
        this.game = game;

        //la dimensione dell'ondata dipende dal numero di nemici contenuti al suo interno
        this.width = this.game.enemySize * this.game.columns;
        this.height = this.game.enemySize * this.game.rows;

        //inizialmente l'ondata è fuori dall'area di gioco (non visibile)
        this.x = 0;
        this.y = -this.height;

        this.speedX = 3;
        this.speedY = 0;
        this.enemies = [];
        this.newWaveCreated = false;    //indica se la prossima ondata è già stata creata o meno
        this.createWave();
    }

    //funzione che effettua il render di un'ondata di nemici
    render(context){
        if(this.y < 0) this.y += 5;     //fa entrare l'ondata nell'area di gioco
        
        this.speedY = 0;

        //nel momento in cui l'ondata raggiunge il bordo dell'area di gioco viene invertito il movimento lungo l'asse x
        //e l'ondata viene abbassata della dimensione di un nemico
        if(this.x < 0 || this.x > this.game.width - this.width){
            this.speedX = -this.speedX;
            this.speedY = this.game.enemySize;
        }
        this.x += this.speedX;
        this.y += this.speedY;
        this.enemies.forEach(enemy => {
            enemy.update(this.x, this.y);
            enemy.draw(context);
        });
        this.enemies = this.enemies.filter(object => !object.eliminated);   //elimino dall'ondata i nemici già eliminati dal giocatore
    }

    //funzione che aggiunge il numero di nemici corretto ad un'ondata nelle rispettive posizioni
    createWave(){
        for(let i = 0; i < this.game.rows; i++){
            for(let j = 0; j < this.game.columns; j++){
                let enemyX = j * this.game.enemySize;
                let enemyY = i * this.game.enemySize;
                this.enemies.push(new Enemy(this.game, enemyX, enemyY));
            }
        }
    }
}

class Game {
    constructor(canvas){
        this.canvas = canvas;
        this.width = this.canvas.width;
        this.height = this.canvas.height;
        this.player = new Player(this);     //creeo una nuova istanza di giocatore
        this.keys = [];     //vettore che conterrà i tasti attualmente premuti dal giocatore

        this.projectiles = [];
        this.numberOfProjectiles = 10;  //numero massimo di proiettili che il giocatore può sparare contemporaneamente
        this.createProjectiles();

        //inizializzazione della prima ondata di nemici
        this.rows = 2;
        this.columns = 2;
        this.enemySize = 60;
        this.waveNumber = 1;
        this.waves = [];
        this.waves.push(new Wave(this));

        this.score = 0;
        this.gameOver = false;

        //gestione dei tasti premuti dal giocatore
        window.addEventListener('keydown', event => {
            if(this.keys.indexOf(event.key) === -1) this.keys.push(event.key);
            if(event.key === ' ') this.player.shoot();
        });

        //quando un tasto viene rilasciato viene eliminato dal vettore keys
        window.addEventListener('keyup', event => {
            let index = this.keys.indexOf(event.key);
            if(index > -1) this.keys.splice(event.key, 1);
        });
    }

    //funzione che effettua il render del gioco
    render(context){
        this.drawGameText(context);
        this.player.update();
        this.player.draw(context);
        this.projectiles.forEach(projectile => {
            projectile.update();
            projectile.draw(context);
        });
        this.waves.forEach(wave => {
            wave.render(context);
            if(wave.enemies.length < 1 && !wave.newWaveCreated && !this.gameOver){
                this.newWave();
                this.waveNumber++;
                wave.newWaveCreated = true;
                this.player.lives++;    //se il giocatore sconfigge un'ondata guadagna una vita
            }
        });
    }

    //funzione per risparmiare memoria e limitare il numero di proiettili a disposizione del giocatore
    createProjectiles(){
        for(let i = 0; i < this.numberOfProjectiles; i++){
            this.projectiles.push(new Projectile());
        }
    }

    //restituisce un proiettile non utilizzato tra quelli disponibili
    getProjectile(){
        for(let i = 0; i < this.numberOfProjectiles; i++){
            if(this.projectiles[i].free) return this.projectiles[i];
        }
    }

    //Funzione che restituisce true in caso di collisione tra due elementi false altrimenti
    collisionDetection(a, b){
        return(
            a.x < b.x + b.width &&
            a.x + a.width > b.x &&
            a.y < b.y + b.height &&
            a.y + a.height > b.y
        );
    }

    //funzione per disegnare l'interfaccia di gioco con punteggio, vite rimanenti e numero di ondata
    //nel caso di game over viene mostrato un messaggio con le informazioni per tornare alla home
    drawGameText(context){
        context.save();
        context.fillText('Score: ' + this.score, 20, 40);
        context.fillText('Wave: '+ this.waveNumber, 20, 70);
        context.fillText('Lives: ' + this.player.lives, 20, 100);
        if(this.gameOver){
            context.textAlign = 'center';
            context.font = '70px PressStart2P';
            context.fillText('GAME OVER', this.width / 2, this.height / 2);
            context.font = '20px PressStart2P';
            context.fillText("Press \"h\" to return to home", this.width / 2, this.height / 2 + 50);
        }

        context.restore();
    }

    //creazione nuova ondata di nemici
    //l'altezza non supera il 60% di quella del canvas
    //la larghezza non supera l'80% di quella del canvas
    newWave(){
        if(Math.random() < 0.5 && this.rows * this.enemySize < this.height * 0.6){
            this.rows++;
        }
        else if(this.columns * this.enemySize < this.width * 0.8){
            this.columns++;
        }
        this.waves.push(new Wave(this));
    }
}

function start(){
    const splash = document.getElementById('splash');
    splash.classList.add('hide');

    //background music
    document.getElementById('bgm').play();

    const canvas = document.getElementById('canvas1');
    const ctx = canvas.getContext('2d');

    //risoluzione del gioco
    canvas.width = 700;
    canvas.height = 900;

    //scelta del font e delle dimensioni per l'interfaccia di gioco
    ctx.fillStyle = 'white';
    ctx.strokeStyle = 'white';
    ctx.lineWidth = 5;
    ctx.font = '20px PressStart2P';

    const game = new Game(canvas);

    function animate(){
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        game.render(ctx);
        window.requestAnimationFrame(animate);
    }
    animate();

    //quando la partita termina e il giocatore preme "h" viene salvato il punteggio registrato e si torna alla home
    window.addEventListener("keydown", event => {
        if((event.key == 'h' || event.key == 'H') && game.gameOver){
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if(this.readyState == 4 && this.status == 200){
                    window.location.replace("/Cosmic-Clash/home/home.php");
                }
            }

            xhttp.open("POST", "function.php", true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send("fname=registraPunteggio&punt=" + game.score);
        }
        return;
    });
}