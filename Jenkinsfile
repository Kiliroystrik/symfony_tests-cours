pipeline {
    agent {
        docker {
            image 'node:16-alpine' // Utilise l'image officielle de Node.js
            args '-v /var/run/docker.sock:/var/run/docker.sock' // Pour accéder au socket Docker
        }
    }

    stages {
        stage('Build Docker Image') {
            steps {
                script {
                    // Construire l'image Docker avec Node.js
                    sh 'docker build -t mon-image-nodejs .'
                }
            }
        }
    }

    post {
        success {
            echo 'L\'image Docker Node.js a été construite avec succès !'
        }
        failure {
            echo 'La construction de l\'image Docker a échoué.'
        }
    }
}
