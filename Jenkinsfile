pipeline {
    agent {
        docker {
            image 'symfony_base-php'
            args '-v /c/ProgramData/Jenkins:/workspace' // Assure un montage correct pour Docker sur Windows
        }
    }

    stages {
        stage('Checkout') {
            steps {
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
