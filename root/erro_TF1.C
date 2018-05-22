double erro_TF1(TF1* f, double x, double* fCov, bool cov = true)
{
  int n = f->GetNpar();
  double* g = new double[n];
  
  double f1,f2,g1,g2,h2,d0,d2;
  double sigma2 = 0;
  double X[1];
  X[0] = x;
  
  for(int i = 0; i<n; i++)  g[i] = f->GradientPar(i,X);
    
  double k = 0;
  for(int i = 0; i<n; i++)
    for(int j = 0; j<n; j++) 
    {
      k = 0;
      if(i==j) k = 1;
      if(cov)  k = 1;
      sigma2 += k*fCov[i*n+j]*g[i]*g[j];
    }
  
  delete [] g;
  
  return sqrt(sigma2);
}

TGraphErrors* funcao_erro(TF1*f, double* fCov, double xmin, double xmax, int np, bool cov = true)
{
  TGraphErrors *g = new TGraphErrors(np);
  double ex = 0;
  double ey = 0;
  double x = xmin;
  double y = 0;
  double dx = (xmax-xmin)/(double)np;
  x-=dx;
  
  for(int i = 0;i<np; i++)
  {
    x+=dx;
    y = f->Eval(x);
    ey = erro_TF1(f,x,fCov,cov);
    g->SetPoint(i,x,y);
    g->SetPointError(i,ex,ey);
  }
  return g;
}
TGraph* funcao_erro_superior(TGraphErrors *g)
{
  int n = g->GetN();
  double *X = g->GetX();
  double *Y = g->GetY();
  double *E = g->GetEY();
  TGraph *g1 = new TGraph(n);
  for(int i = 0; i<n; i++) g1->SetPoint(i,X[i],Y[i]+E[i]);
  return g1;
}
TGraph* funcao_erro_inferior(TGraphErrors *g)
{
  int n = g->GetN();
  double *X = g->GetX();
  double *Y = g->GetY();
  double *E = g->GetEY();
  TGraph *g1 = new TGraph(n);
  for(int i = 0; i<n; i++) g1->SetPoint(i,X[i],Y[i]-E[i]);  
  return g1;
}
