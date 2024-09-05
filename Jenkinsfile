pipeline {
    agent {
        docker {
            // Assurez-vous que l'image Docker est correcte et disponible
            image 'symfony_base-php' // Remplacer par votre image Docker correcte
        }
    }

    stages {
        stage('Checkout') {
            steps {
                // Cloner le dépôt Git
                git branch: 'main', url: 'https://github.com/Kiliroystrik/symfony_tests-cours.git'
            }
        }
        
        stage('Install') {
            steps {
                script {
                    // Utiliser 'sh' car nous sommes dans un environnement Linux (Docker)
                    sh 'composer install --no-interaction --prefer-dist'
                }
            }
        }
        
        stage('Tests') {
            steps {
                script {
                    // Utiliser 'sh' pour exécuter PHPUnit sur un environnement Linux
                    sh './vendor/bin/phpunit'
                }
            }
        }
    }
}
