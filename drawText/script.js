var text = '';

function drawText(inputText){
    text = inputText;   //testo da visualizzare
    const canvas = document.getElementById('canvas1');
    const ctx = canvas.getContext('2d', {   //ottimizzazione del canvas nel caso di molti accessi
        willReadFrequently: true
    });
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    class Particle {
        constructor(effect, x, y, color) {
            this.effect = effect;

            //posizione iniziale delle particelle
            this.x = x;
            this.y = this.effect.canvasHeight;  //le particelle entrano nell'area visibile dal basso

            this.color = color;

            this.originY = y;   //posizione finale sull'asse y
            this.size = this.effect.gap - Math.random() * 5;    //le particelle hanno una dimensione casuale

            this.vy = -10;      //velocità con cui le particelle si portano in posizione
        }

        //funzione che disegna una particella
        draw() {
            this.effect.context.fillStyle = this.color;
            this.effect.context.fillRect(this.x, this.y, this.size, this.size);
        }

        //aggiorna la posizione delle particelle
        update() {
            this.y = (this.y <= this.originY) ? this.originY : this.y + this.vy;
        }
    }

    class Effect {
        constructor(context, canvasWidth, canvasHeight) {
            this.context = context;
            this.canvasWidth = canvasWidth;
            this.canvasHeight = canvasHeight;
            this.textX = canvasWidth / 2;
            this.textY = canvasHeight / 2;
            this.fontSize = 150;
            this.lineHeight = this.fontSize * 0.9;
            this.maxTextWidth = canvasWidth * 0.65;

            this.particles = [];
            this.gap = 4;
        }

        divideText(text) {
            //creazione gradiente per il colore del testo da visualizzare
            const gradient = this.context.createLinearGradient(0, 0, canvas.width, canvas.height);
            gradient.addColorStop(0.3, 'blue');     //(offset%, colore)
            gradient.addColorStop(0.5, 'purple');
            gradient.addColorStop(0.7, 'magenta');
            this.context.fillStyle = gradient;

            //diemensione, tipo di font e allineamento del testo da visualizzare
            this.context.font = this.fontSize + 'px Courier New';
            this.context.textAlign = 'center';
            this.context.textBaseline = 'middle';
            this.context.lineWidth = 5;
            this.context.strokeStyle = gradient;
            
            //separare il testo su più righe
            let lineArray = [];
            let lineCounter = 0;
            let line = '';
            let words = text.split(' ');    //separo il testo in parole

            for (let i = 0; i < words.length; i++){
                let testLine = line + words[i] + ' ';

                //se la dimensione di una riga supera la massima dimensione consentita creo una nuova riga
                if (this.context.measureText(testLine).width > this.maxTextWidth){  
                    line = words[i] + ' ';
                    lineCounter++;
                } else {
                    line = testLine;
                }
                lineArray[lineCounter] = line;  //inserimento della riga nel vettore contenente il testo da visualizzare
            }

            //calcolo posizione in cui disegnare il testo affinché sia centrato
            let textHeight = this.lineHeight * lineCounter;
            this.textY = canvas.height / 2 - textHeight / 2;

            //diesegno il testo nella posizione calcolata
            lineArray.forEach((el, index) => {
                this.context.fillText(el, this.textX, this.textY + (index * this.lineHeight));
                this.context.strokeText(el, this.textX, this.textY + (index * this.lineHeight));
            });

            this.convertToParticles(); //converto il testo in particelle
        }

        convertToParticles(){
            this.particles = [];

            /*pixels dell'immagine contenente la scritta da visualizzare
            per ogni pixel si hanno 4 informazioni a diversi indici:
                0 : R (Red) [compreso tra 0 e 255]
                1 : G (Green) [compreso tra 0 e 255]
                2 : B (Blue) [compreso tra 0 e 255]
                3 : A (Aplha) [compreso tra 0(trasparente) e 255(completamente visibile)]
            */
            const pixels = this.context.getImageData(0, 0, this.canvasWidth, this.canvasHeight).data;

            //elimino la scritta precedente dal canvas
            this.context.clearRect(0, 0, this.canvasWidth, this.canvasHeight); 

            //suddivisione dell'immagine in quadrati di dimensione 4px * 4x
            for (let y = 0; y < this.canvasHeight; y+=this.gap) {
                for ( let x = 0; x < this.canvasWidth; x+=this.gap) {
                    const index = (y * this.canvasWidth + x) * 4;   //calcolo indice del quadrato all'interno del vettore di pixels
                    const alpha = pixels[index + 3];    //valore del parametro alpha
                    if (alpha > 0){     //se il quadrato è visibile questo corrisponderà ad una particella
                        const red = pixels[index];
                        const green = pixels[index + 1];
                        const blue = pixels[index + 2];
                        const color = 'rgb(' + red + ',' + green + ',' + blue + ')';
                        this.particles.push(new Particle(this, x, y, color));
                    }
                }
            }
        }

        //funzione che effettua il render del testo
        render() {
            this.particles.forEach(particle => {
                particle.update();
                particle.draw();
            });
        }

        //funzione che effettua il ridimensionamento degli elementi nel caso in cui le dimensioni della finesta cambino
        resize(width, height){
            this.canvasWidth = width;
            this.canvasHeight = height;
            this.textX = this.canvasWidth / 2;
            this.textY = this.canvasHeight / 2;
            this.maxTextWidth = this.canvasWidth * 0.45;
        }
    }

    const effect = new Effect(ctx, canvas.width, canvas.height);
    effect.divideText(text);      //suddivisione in righe del testo da disegnare
    effect.render();

    function animate(){
        ctx.clearRect(0, 0, canvas.width, canvas.height);   //elimino il contenuto del canvas precendente
        effect.render();    //aggiorno il contenuto del canvas
        requestAnimationFrame(animate);
    }
    animate();

    //gestione eventuale ridimensionamento della finestra
    this.window.addEventListener('resize', function(){
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        effect.resize(canvas.width, canvas.height);
        effect.divideText(text);
    });
}