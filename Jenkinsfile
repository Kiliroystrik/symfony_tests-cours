pipeline {
    agent {
        docker {
            image '9ff443428e6fe6964cb72dbc20297b434dbb98635fec1f686ec6b54305772432'
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
                    // Installer les dépendances Composer
                    sh 'composer install --no-interaction --prefer-dist'
                }
            }
        }

        stage('Tests') {
            steps {
                script {
                    // Lancer PHPUnit
                    sh './vendor/bin/phpunit'
                }
            }
        }
    }
}
